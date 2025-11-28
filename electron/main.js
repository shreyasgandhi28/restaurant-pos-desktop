// main.js - robust first-run initializer
const { app, BrowserWindow, dialog, ipcMain, shell } = require('electron');
const { spawnSync, spawn } = require('child_process');
const path = require('path');
const fs = require('fs-extra');
const axios = require('axios');

let phpProcess;
let mainWindow;

function isPackaged() {
  return app.isPackaged;
}

function packagedPath(...parts) {
  if (!isPackaged()) {
    return path.join(__dirname, '..', ...parts);
  }
  return path.join(process.resourcesPath, ...parts);
}

function getPhpBinary() {
  if (!isPackaged()) {
    return path.join(__dirname, '..', 'runtime', 'php', process.platform === 'win32' ? 'php.exe' : 'php');
  }
  return path.join(process.resourcesPath, 'runtime', 'php', process.platform === 'win32' ? 'php.exe' : 'php');
}

/**
 * runPhpArtisan - run an artisan command synchronously
 */
function runPhpArtisan(argsArray, cwd) {
  const php = getPhpBinary();
  try {
    const res = spawnSync(php, ['artisan', ...argsArray], {
      cwd,
      env: Object.assign({}, process.env, { APP_ENV: 'production' }),
      windowsHide: true,
      encoding: 'utf8',
      maxBuffer: 20 * 1024 * 1024
    });
    return { status: res.status, stdout: res.stdout, stderr: res.stderr };
  } catch (e) {
    return { status: 1, stdout: '', stderr: String(e) };
  }
}

/**
 * FIRST-RUN INIT & UPDATE LOGIC
 */
async function firstRunInit() {
  let runtimeBackend;
  let targetDb;
  let userDataDir;

  // -------------------------------------------------------------------------
  // 1. PATH RESOLUTION (Dev vs Prod)
  // -------------------------------------------------------------------------
  if (!isPackaged()) {
    console.log('Running in DEV MODE');
    runtimeBackend = path.join(__dirname, '..', 'backend');
    userDataDir = app.getPath('userData'); // Still use UserData for consistency if needed, but we use local backend

    // In Dev, DB is in the backend folder
    targetDb = path.join(runtimeBackend, 'database', 'database.sqlite');

    // Ensure DB exists for dev
    if (!await fs.pathExists(targetDb)) {
      await fs.ensureFile(targetDb);
    }

  } else {
    // PRODUCTION MODE
    const appData = app.getPath('appData');
    userDataDir = path.join(appData, 'RestaurantPOS');
    await fs.ensureDir(userDataDir);

    const packagedBackend = packagedPath('backend');
    const userBackend = path.join(userDataDir, 'backend');
    const versionFile = path.join(userDataDir, 'version.json');

    // Determine if we need to update
    let currentVersion = app.getVersion();
    let storedVersion = '0.0.0';
    if (await fs.pathExists(versionFile)) {
      try {
        const vData = await fs.readJson(versionFile);
        storedVersion = vData.version;
      } catch (e) { }
    }

    const needsUpdate = (currentVersion !== storedVersion) || (!await fs.pathExists(userBackend));

    if (needsUpdate) {
      console.log(`Updating from ${storedVersion} to ${currentVersion}...`);

      // Preserve User Data
      const tempStorage = path.join(userDataDir, 'storage_temp');
      const tempEnv = path.join(userDataDir, '.env.temp');
      const userStorage = path.join(userBackend, 'storage');
      const userEnv = path.join(userBackend, '.env');

      if (await fs.pathExists(userEnv)) await fs.copy(userEnv, tempEnv);
      if (await fs.pathExists(userStorage)) await fs.move(userStorage, tempStorage, { overwrite: true });

      // Wipe and Copy
      await fs.emptyDir(userBackend);
      await fs.copy(packagedBackend, userBackend, {
        filter: (src) => {
          const p = src.replace(/\\/g, '/').toLowerCase();
          return !p.includes('/node_modules/') && !p.includes('/.git/');
        }
      });

      // Restore
      if (await fs.pathExists(tempEnv)) await fs.move(tempEnv, userEnv, { overwrite: true });
      if (await fs.pathExists(tempStorage)) {
        await fs.copy(tempStorage, userStorage, { overwrite: true });
        await fs.remove(tempStorage);
      }

      await fs.writeJson(versionFile, { version: currentVersion });
    }

    runtimeBackend = userBackend;

    // DB Handling for Prod
    targetDb = path.join(userDataDir, 'database.sqlite');
    const packagedDB = path.join(packagedBackend, 'database', 'database.sqlite');

    if (!await fs.pathExists(targetDb) || (await fs.stat(targetDb)).size === 0) {
      if (await fs.pathExists(packagedDB) && (await fs.stat(packagedDB)).size > 0) {
        await fs.copy(packagedDB, targetDb);
      } else {
        await fs.ensureFile(targetDb);
      }
    }

    // Ensure public/storage symlink is removed so Laravel route handles it
    const symlinkPath = path.join(runtimeBackend, 'public', 'storage');
    try {
      if (fs.existsSync(symlinkPath) || fs.lstatSync(symlinkPath).isSymbolicLink()) {
        await fs.remove(symlinkPath);
      }
    } catch (e) { }
  }

  // -------------------------------------------------------------------------
  // 2. RUNTIME CONFIGURATION (.env) - Runs for BOTH Dev and Prod
  // -------------------------------------------------------------------------
  const runtimeEnvPath = path.join(runtimeBackend, '.env');
  let envText = '';
  if (await fs.pathExists(runtimeEnvPath)) {
    envText = await fs.readFile(runtimeEnvPath, 'utf8');
  }

  // Helper to enforce env vars
  const forceEnv = (key, val) => {
    const regex = new RegExp(`^${key}=.*`, 'gm');
    if (envText.match(regex)) {
      envText = envText.replace(regex, `${key}=${val}`);
    } else {
      envText += `\n${key}=${val}`;
    }
  };

  // CRITICAL: Fixes 419 Page Expired (Session Issues)
  forceEnv('APP_ENV', 'production');
  forceEnv('APP_DEBUG', isPackaged() ? 'false' : 'true');

  forceEnv('APP_URL', 'http://127.0.0.1:8000');
  forceEnv('SESSION_DRIVER', 'file');
  forceEnv('SESSION_DOMAIN', 'null');
  forceEnv('SESSION_SECURE_COOKIE', 'false');
  forceEnv('SANCTUM_STATEFUL_DOMAINS', '127.0.0.1:8000');

  // DB Path
  const dbForEnv = targetDb.split(path.sep).join(path.posix.sep);
  forceEnv('DB_DATABASE', dbForEnv);

  await fs.writeFile(runtimeEnvPath, envText, 'utf8');

  // -------------------------------------------------------------------------
  // 3. STORAGE & CACHE - Runs for BOTH
  // -------------------------------------------------------------------------
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'framework', 'sessions'));
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'framework', 'views'));
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'logs'));

  // Clear cache to ensure new .env is read
  try { runPhpArtisan(['config:clear'], runtimeBackend); } catch (e) { }
  try { runPhpArtisan(['view:clear'], runtimeBackend); } catch (e) { }
  try { runPhpArtisan(['cache:clear'], runtimeBackend); } catch (e) { }

  // Generate Key if missing
  if (!envText.includes('APP_KEY=') || envText.match(/APP_KEY=\s*$/m)) {
    runPhpArtisan(['key:generate', '--force'], runtimeBackend);
  }

  // Migrate
  try {
    runPhpArtisan(['migrate', '--force'], runtimeBackend);
  } catch (e) { }

  return { packagedBackend: runtimeBackend, targetDb, userDataDir };
}

function startPhpServer(packagedBackend) {
  const phpBin = getPhpBinary();
  if (!fs.existsSync(phpBin)) {
    const errorMsg = `Bundled PHP not found at: ${phpBin}\n\nPlease try:\n1. Running as Administrator\n2. Temporarily disabling antivirus\n3. Reinstalling the application`;
    dialog.showErrorBox('Startup Error', errorMsg);
    app.quit();
    return;
  }

  const publicDir = path.join(packagedBackend, 'public');
  const routerScript = path.join(packagedBackend, 'router.php');

  // Create log file for debugging
  const logDir = app.getPath('userData');
  const logFile = path.join(logDir, 'php-server.log');
  const logStream = fs.createWriteStream(logFile, { flags: 'a' });

  logStream.write(`\n\n=== PHP Server Start: ${new Date().toISOString()} ===\n`);
  logStream.write(`PHP Binary: ${phpBin}\n`);
  logStream.write(`Backend Dir: ${packagedBackend}\n`);
  logStream.write(`Public Dir: ${publicDir}\n`);
  logStream.write(`Router Script: ${routerScript}\n\n`);

  phpProcess = spawn(phpBin, ['-S', '127.0.0.1:8000', '-t', publicDir, routerScript], {
    cwd: packagedBackend,
    env: Object.assign({}, process.env, { APP_ENV: 'production' }),
    windowsHide: true
  });

  phpProcess.stdout.on('data', d => {
    const msg = d.toString();
    console.log('[PHP]', msg);
    logStream.write('[STDOUT] ' + msg);
  });

  phpProcess.stderr.on('data', d => {
    const msg = d.toString();
    console.error('[PHP ERR]', msg);
    logStream.write('[STDERR] ' + msg);
  });

  phpProcess.on('error', (err) => {
    const msg = `PHP Process Error: ${err.message}\n`;
    console.error(msg);
    logStream.write(msg);
  });

  phpProcess.on('exit', (code, signal) => {
    const msg = `PHP Process exited with code ${code}, signal ${signal}\n`;
    console.log(msg);
    logStream.write(msg);
    logStream.end();
  });
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 820,
    icon: path.join(__dirname, '../build/icon.ico'),
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      preload: path.join(__dirname, 'preload.js')
    }
  });

  mainWindow.loadURL('http://127.0.0.1:8000/');
}

ipcMain.on('open-external', (event, url) => {
  shell.openExternal(url);
});

app.whenReady().then(async () => {
  try {
    const { packagedBackend } = await firstRunInit();
    startPhpServer(packagedBackend);

    // Poll for server - increased timeout for first-run and slower machines
    const max = 120; // 60 seconds total (120 × 500ms)
    let i = 0;
    const wait = ms => new Promise(r => setTimeout(r, ms));
    let lastError = null;

    while (i < max) {
      try {
        await axios.get('http://127.0.0.1:8000', { timeout: 2000 });
        break;
      } catch (e) {
        lastError = e;
        await wait(500);
        i++;
      }
    }

    if (i === max) {
      const logFile = path.join(app.getPath('userData'), 'php-server.log');
      const errorDetails = lastError ? `\n\nLast error: ${lastError.message}` : '';
      const troubleshooting = `
Troubleshooting steps:
1. Check if port 8000 is available (close other apps)
2. Temporarily disable antivirus/Windows Defender
3. Run as Administrator
4. Check log file at: ${logFile}

If problem persists, send the log file to support.`;

      throw new Error(`Local server did not start in time.${errorDetails}\n${troubleshooting}`);
    }

    createWindow();
  } catch (err) {
    dialog.showErrorBox('Startup Error', err.message || String(err));
    app.quit();
  }
});

app.on('before-quit', () => {
  if (phpProcess) phpProcess.kill();
});
