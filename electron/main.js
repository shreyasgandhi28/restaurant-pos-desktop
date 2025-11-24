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
    // dev: project/electron folder inside repo -> runtime and backend are ../runtime and ../backend
    return path.join(__dirname, '..', ...parts);
  }
  // packaged: resources path points to ...\resources
  return path.join(process.resourcesPath, ...parts);
}

function getPhpBinary() {
  if (!isPackaged()) {
    return path.join(__dirname, '..', 'runtime', 'php', process.platform === 'win32' ? 'php.exe' : 'php');
  }
  return path.join(process.resourcesPath, 'runtime', 'php', process.platform === 'win32' ? 'php.exe' : 'php');
}

/**
 * runPhpArtisan - run an artisan command synchronously and return {status, stdout, stderr}
 * Uses spawnSync so we can detect failures during first-run.
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
 * FIRST-RUN INIT: copy packaged backend to AppData; ensure DB, .env, APP_KEY, sessions file-driver etc.
 */
async function firstRunInit() {
  const appData = app.getPath('appData'); // C:\Users\<user>\AppData\Roaming
  const userDataDir = path.join(appData, 'RestaurantPOS');
  await fs.ensureDir(userDataDir);

  // packaged backend lives in resources/backend (packaged) or ../backend (dev)
  const packagedBackend = packagedPath('backend');

  // We'll keep a writable runtime copy inside AppData\RestaurantPOS\backend
  const userBackend = path.join(userDataDir, 'backend');

  // Copy packaged backend to AppData if not already present
  if (!await fs.pathExists(userBackend)) {
    // Copy but skip heavy folders that we don't need to edit (devDependencies) - safe filter
    await fs.copy(packagedBackend, userBackend, {
      filter: (src) => {
        const p = src.replace(/\\/g, '/').toLowerCase();
        // skip node_modules from packaged backend (we already ship vendor)
        if (p.includes('/node_modules/')) return false;
        // skip .git if present
        if (p.includes('/.git/')) return false;
        return true;
      }
    });
  }

  // Paths we will use
  const runtimeBackend = userBackend; // writable backend copy used at runtime
  const packagedDB = path.join(packagedBackend, 'database', 'database.sqlite');
  const targetDb = path.join(userDataDir, 'database.sqlite');
  const targetStorage = path.join(userDataDir, 'storage');

  // Ensure storage folder in AppData exists (copy packaged storage if present)
  if (!await fs.pathExists(targetStorage)) {
    const packagedStorage = path.join(packagedBackend, 'storage');
    if (await fs.pathExists(packagedStorage)) {
      await fs.copy(packagedStorage, targetStorage);
    } else {
      await fs.ensureDir(targetStorage);
    }
  }

  // Ensure DB exists in AppData (copy packaged DB if available)
  if (!await fs.pathExists(targetDb) || (await fs.stat(targetDb)).size === 0) {
    if (await fs.pathExists(packagedDB) && (await fs.stat(packagedDB)).size > 0) {
      await fs.copy(packagedDB, targetDb);
    } else {
      // create empty file so artisan migrate can run
      await fs.ensureFile(targetDb);
    }
  }

  // Prepare runtime .env inside the writable backend copy (runtimeBackend/.env)
  const runtimeEnvPath = path.join(runtimeBackend, '.env');
  let envText = '';
  if (await fs.pathExists(runtimeEnvPath)) {
    envText = await fs.readFile(runtimeEnvPath, 'utf8');
  }

  // Force desktop-safe env settings
  envText = envText.replace(/APP_ENV=.*/g, 'APP_ENV=production');
  if (!envText.includes('APP_ENV=')) envText += '\nAPP_ENV=production\n';

  envText = envText.replace(/APP_DEBUG=.*/g, 'APP_DEBUG=false');
  if (!envText.includes('APP_DEBUG=')) envText += '\nAPP_DEBUG=false\n';

  envText = envText.replace(/APP_URL=.*/g, 'APP_URL=http://127.0.0.1:8000');
  if (!envText.includes('APP_URL=')) envText += '\nAPP_URL=http://127.0.0.1:8000\n';

  // Force file session driver for desktop
  if (envText.includes('SESSION_DRIVER=')) {
    envText = envText.replace(/SESSION_DRIVER=.*/g, 'SESSION_DRIVER=file');
  } else {
    envText += '\nSESSION_DRIVER=file\n';
  }

  // Session domain + secure cookie defaults
  if (envText.includes('SESSION_DOMAIN=')) {
    envText = envText.replace(/SESSION_DOMAIN=.*/g, 'SESSION_DOMAIN=null');
  } else {
    envText += '\nSESSION_DOMAIN=null\n';
  }
  if (envText.includes('SESSION_SECURE_COOKIE=')) {
    envText = envText.replace(/SESSION_SECURE_COOKIE=.*/g, 'SESSION_SECURE_COOKIE=false');
  } else {
    envText += '\nSESSION_SECURE_COOKIE=false\n';
  }

  // DB path absolute (convert to forward slashes to be safe in .env)
  const dbForEnv = targetDb.split(path.sep).join(path.posix.sep);
  if (envText.includes('DB_DATABASE=')) {
    envText = envText.replace(/DB_DATABASE=.*/g, `DB_DATABASE=${dbForEnv}`);
  } else {
    envText += `\nDB_DATABASE=${dbForEnv}\n`;
  }

  // Write .env to runtime backend
  await fs.writeFile(runtimeEnvPath, envText, 'utf8');

  // Ensure storage folders inside runtimeBackend are present and writable
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'framework', 'sessions'));
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'framework', 'views'));
  await fs.ensureDir(path.join(runtimeBackend, 'storage', 'logs'));

  // Remove cached config / routes / views so Laravel re-reads .env
  const bootstrapCacheDir = path.join(runtimeBackend, 'bootstrap', 'cache');
  try { await fs.remove(path.join(bootstrapCacheDir, 'config.php')); } catch (e) { }
  try { await fs.remove(path.join(bootstrapCacheDir, 'routes-v7.php')); } catch (e) { }
  try { await fs.remove(path.join(bootstrapCacheDir, 'packages.php')); } catch (e) { }
  try { await fs.remove(path.join(bootstrapCacheDir, 'services.php')); } catch (e) { }

  // Ensure APP_KEY exists: try artisan key:generate, else create random base64 key
  let hasAppKey = /APP_KEY=/.test(envText);
  if (!hasAppKey) {
    const resKey = runPhpArtisan(['key:generate', '--force'], runtimeBackend);
    if (resKey.status !== 0) {
      // fallback random key
      try {
        const crypto = require('crypto');
        const randomKey = 'base64:' + crypto.randomBytes(32).toString('base64');
        envText = envText.replace(/APP_KEY=.*/g, '');
        envText += `\nAPP_KEY=${randomKey}\n`;
        await fs.writeFile(runtimeEnvPath, envText, 'utf8');
      } catch (e) {
        // ignore
      }
    } else {
      // if artisan succeeded, reload envText from file
      envText = await fs.readFile(runtimeEnvPath, 'utf8');
    }
  }

  // Attempt to clear config and views (best-effort)
  try { runPhpArtisan(['config:clear'], runtimeBackend); } catch (e) { }
  try { runPhpArtisan(['view:clear'], runtimeBackend); } catch (e) { }

  // If DB file is empty, try to run migrations (best-effort)
  try {
    const stats = await fs.stat(targetDb);
    if (stats.size === 0) {
      const migrateRes = runPhpArtisan(['migrate', '--force'], runtimeBackend);
      if (migrateRes.status !== 0) {
        const logPath = path.join(userDataDir, 'first_run_errors.log');
        await fs.appendFile(logPath, `Migrate failed: stdout=${migrateRes.stdout}\nstderr=${migrateRes.stderr}\n`, 'utf8');
      }
    }
  } catch (e) {
    // ignore
  }

  return { packagedBackend: runtimeBackend, targetDb, userDataDir };
}

function startPhpServer(packagedBackend) {
  const phpBin = getPhpBinary();
  if (!fs.existsSync(phpBin)) {
    dialog.showErrorBox('Startup Error', `Bundled PHP not found: ${phpBin}\nPlease reinstall or rebuild the installer with runtime files included.`);
    app.quit();
    return;
  }

  const publicDir = path.join(packagedBackend, 'public');

  phpProcess = spawn(phpBin, ['-S', '127.0.0.1:8000', '-t', publicDir], {
    cwd: packagedBackend,
    env: Object.assign({}, process.env, { APP_ENV: 'production' }),
    windowsHide: true
  });

  phpProcess.stdout.on('data', d => console.log('[PHP]', d.toString()));
  phpProcess.stderr.on('data', d => console.error('[PHP ERR]', d.toString()));
  phpProcess.on('exit', code => console.log('[PHP exited]', code));
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 820,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      preload: path.join(__dirname, 'preload.js')
    }
  });

  mainWindow.loadURL('http://127.0.0.1:8000/');
}

// IPC handler to open URLs in default browser
ipcMain.on('open-external', (event, url) => {
  shell.openExternal(url);
});


app.whenReady().then(async () => {
  try {
    const { packagedBackend } = await firstRunInit();

    // Start php server using the writable runtime backend copy
    startPhpServer(packagedBackend);

    // Wait until server responds (poll)
    const max = 30;
    let i = 0;
    const wait = ms => new Promise(r => setTimeout(r, ms));
    while (i < max) {
      try {
        await axios.get('http://127.0.0.1:8000', { timeout: 2000 });
        break;
      } catch (e) {
        await wait(500);
        i++;
      }
    }

    if (i === max) {
      throw new Error('Local server did not start in time. Check runtime/php/php.exe or VC++ redistributable. Check first_run_errors.log in AppData if present.');
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
