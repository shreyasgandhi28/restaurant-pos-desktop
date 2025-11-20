<script setup>
import { ref, onMounted, onUnmounted, onErrorCaptured } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { CurrencyDollarIcon, PlusIcon, UserPlusIcon } from '@heroicons/vue/24/outline';

// Error handling
const error = ref(null);

// Log any component errors
onErrorCaptured((err) => {
    console.error('Component error:', err);
    error.value = err.message || 'An error occurred';
    return false; // Prevent the error from propagating further
});

// Log props when component is mounted
onMounted(() => {
    console.log('Component mounted with props:', {
        advances: props.advances,
        staff: props.staff,
        summary: props.summary,
        paymentMethods: props.paymentMethods
    });
    
    // Check if we should show the staff modal from URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('showStaff') === 'true') {
        showStaffModal.value = true;
    }
});

// Define props
const props = defineProps({
    advances: {
        type: Object,
        default: () => ({ data: [], links: [] })
    },
    staff: {
        type: Array,
        default: () => []
    },
    summary: {
        type: Array,
        default: () => []
    },
    paymentMethods: {
        type: Array,
        default: () => []
    }
});

// Refs for form state
const showAddForm = ref(false);
const showNotesModal = ref(false);
const showAddStaffForm = ref(false);
const showStaffModal = ref(false);
const selectedAdvance = ref(null);
const isSubmitting = ref(false);

// Staff form
const staffForm = useForm({
    name: '',
    email: '',
    role: 'staff'
});

const submitStaffForm = () => {
    console.log('Submitting staff form...', staffForm.data());
    isSubmitting.value = true;
    staffForm.post(route('staff.store'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Staff added successfully');
            staffForm.reset();
            showAddStaffForm.value = false;
            isSubmitting.value = false;
            // Refresh the staff list and summary to update counts
            router.reload({ only: ['staff', 'summary'] });
        },
        onError: (errors) => {
            console.error('Error adding staff:', errors);
            isSubmitting.value = false;
        }
    });
};

// Debug function to test button click
const handleAddStaffClick = () => {
    console.log('Add Staff button clicked!');
    showAddStaffForm.value = true;
    console.log('showAddStaffForm value:', showAddStaffForm.value);
};

// Form handling
const form = useForm({
    user_id: '',
    amount: '',
    payment_method: 'cash',
    notes: '',
    advance_date: new Date().toISOString().split('T')[0]
});

// Handle navigation events
const handleNavigation = (event) => {
    // Allow default navigation
    return true;
};

// Add navigation event listener
onMounted(() => {
    window.addEventListener('popstate', handleNavigation);
});

// Cleanup event listener
onUnmounted(() => {
    window.removeEventListener('popstate', handleNavigation);
    showAddForm.value = false;
    showNotesModal.value = false;
    selectedAdvance.value = null;
    isSubmitting.value = false;
});

const formatCurrency = (value) => {
    if (!value) return '₹0.00';
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(Number(value));
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const formatPaymentMethod = (method) => {
    const methods = {
        cash: 'Cash',
        bank_transfer: 'Bank Transfer',
        cheque: 'Cheque',
        upi: 'UPI',
        other: 'Other'
    };
    return methods[method] || method;
};

const viewNotes = (advance) => {
    selectedAdvance.value = advance;
    showNotesModal.value = true;
};

const submitForm = () => {
    isSubmitting.value = true;
    form.post(route('staff-salary-advances.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showAddForm.value = false;
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
  <div class="min-h-screen bg-gray-100">
  <AppLayout>
    <template #header>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Staff Salary Advances</h1>
          <p class="text-gray-600">Manage staff salary advances and payments</p>
        </div>
      </div>
    </template>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Error Boundary -->
    <div v-if="error" class="bg-red-50 border-l-4 border-red-400 p-4 my-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">
            {{ error }}
          </p>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Total Advances KPI -->
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-green-500 bg-opacity-10">
                  <CurrencyDollarIcon class="w-6 h-6 text-green-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Advances Given
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ formatCurrency(summary.reduce((sum, staff) => sum + (parseFloat(staff.total_advances) || 0), 0)) }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Total Staff KPI -->
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-blue-500 bg-opacity-10">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Staff
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ summary.length }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Staff Summary Cards -->
        <div v-if="summary && summary.length > 0" class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
          <div 
            v-for="staffMember in summary" 
            :key="staffMember.id"
            class="overflow-hidden bg-white rounded-lg shadow"
          >
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-indigo-500 bg-opacity-10">
                  <CurrencyDollarIcon class="w-6 h-6 text-indigo-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      {{ staffMember.name }}
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ formatCurrency(staffMember.total_advances || 0) }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Empty state - only show if summary is empty array (not loading) -->
        <div v-else-if="summary && summary.length === 0" class="mb-6 p-4 bg-gray-50 rounded-lg">
          <p class="text-gray-500 text-center">No staff members with salary advances yet.</p>
        </div>

        <!-- Advances Table -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Salary Advances
            </h3>
            <div class="flex gap-3">
              <button
                @click="showAddStaffForm = true"
                type="button"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border-2 border-indigo-700 rounded-md text-sm font-bold text-white uppercase tracking-wide shadow-lg hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
              >
                <UserPlusIcon class="w-5 h-5 mr-2" />
                Add Staff
              </button>
              <button
                @click="showAddForm = true"
                type="button"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border-2 border-indigo-700 rounded-md text-sm font-bold text-white uppercase tracking-wide shadow-lg hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
              >
                <PlusIcon class="w-5 h-5 mr-2" />
                Add Advance
              </button>
            </div>
          </div>
          
          <div class="overflow-hidden overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Staff
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Amount
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Payment Method
                  </th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Status
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <template v-if="advances && advances.data && advances.data.length > 0">
                  <tr v-for="advance in advances.data" :key="advance.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ advance.user?.name || 'Unknown' }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ advance.user?.designation || 'Staff' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatCurrency(advance.amount) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ formatDate(advance.advance_date) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                        {{ formatPaymentMethod(advance.payment_method) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                        {{ advance.status || 'Completed' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                      <button
                        v-if="advance.notes"
                        @click="viewNotes(advance)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        View Notes
                      </button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    No salary advances found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div v-if="advances && advances.links && advances.links.length > 3" class="flex items-center justify-between px-6 py-3 bg-gray-50">
            <div class="flex justify-between flex-1 sm:hidden">
              <Link 
                v-if="advances.prev_page_url" 
                :href="advances.prev_page_url" 
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link 
                v-if="advances.next_page_url" 
                :href="advances.next_page_url" 
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  {{ ' ' }}
                  <span class="font-medium">{{ advances.from }}</span>
                  {{ ' ' }}
                  to
                  {{ ' ' }}
                  <span class="font-medium">{{ advances.to }}</span>
                  {{ ' ' }}
                  of
                  {{ ' ' }}
                  <span class="font-medium">{{ advances.total }}</span>
                  {{ ' ' }}
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <Link 
                    v-for="(link, i) in advances.links" 
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 text-sm font-medium',
                      link.active 
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      i === 0 ? 'rounded-l-md' : '',
                      i === advances.links.length - 1 ? 'rounded-r-md' : ''
                    ]"
                    :disabled="!link.url"
                  />
                </nav>
              </div>
            </div>
          </div>
        </div>

    <!-- Add Advance Modal -->
    <Modal :show="showAddForm" @close="showAddForm = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Record Salary Advance
        </h2>

        <div class="mt-6">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Staff Member -->
            <div class="sm:col-span-2">
              <label for="user_id" class="block text-sm font-medium text-gray-700">
                Staff Member <span class="text-red-500">*</span>
              </label>
              <select
                id="user_id"
                v-model="form.user_id"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                :class="{ 'border-red-300': form.errors.user_id }"
              >
                <option value="">Select Staff Member</option>
                <option 
                  v-for="staffMember in staff" 
                  :key="staffMember.id" 
                  :value="staffMember.id"
                >
                  {{ staffMember.name }}
                </option>
              </select>
              <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">
                {{ form.errors.user_id }}
              </p>
            </div>

            <!-- Amount -->
            <div>
              <label for="amount" class="block text-sm font-medium text-gray-700">
                Amount <span class="text-red-500">*</span>
              </label>
              <div class="relative mt-1 rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"> ₹ </span>
                </div>
                <input
                  type="number"
                  id="amount"
                  v-model="form.amount"
                  class="block w-full pl-10 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500"
                  :class="{ 'border-red-300': form.errors.amount }"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                />
              </div>
              <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                {{ form.errors.amount }}
              </p>
            </div>

            <!-- Payment Method -->
            <div>
              <label for="payment_method" class="block text-sm font-medium text-gray-700">
                Payment Method <span class="text-red-500">*</span>
              </label>
              <select
                id="payment_method"
                v-model="form.payment_method"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                :class="{ 'border-red-300': form.errors.payment_method }"
              >
                <option 
                  v-for="method in paymentMethods" 
                  :key="method" 
                  :value="method"
                >
                  {{ formatPaymentMethod(method) }}
                </option>
              </select>
              <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-600">
                {{ form.errors.payment_method }}
              </p>
            </div>

            <!-- Date -->
            <div>
              <label for="advance_date" class="block text-sm font-medium text-gray-700">
                Date <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                id="advance_date"
                v-model="form.advance_date"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                :class="{ 'border-red-300': form.errors.advance_date }"
              />
              <p v-if="form.errors.advance_date" class="mt-1 text-sm text-red-600">
                {{ form.errors.advance_date }}
              </p>
            </div>

            <!-- Notes -->
            <div class="sm:col-span-2">
              <label for="notes" class="block text-sm font-medium text-gray-700">
                Notes (Optional)
              </label>
              <div class="mt-1">
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  :class="{ 'border-red-300': form.errors.notes }"
                />
              </div>
              <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                {{ form.errors.notes }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            type="button"
            @click="showAddForm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="submitForm"
            :disabled="isSubmitting"
            class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!isSubmitting">Save Advance</span>
            <span v-else>Saving...</span>
          </button>
        </div>
      </div>
    </Modal>

    <!-- Manage Staff Modal -->
    <Modal :show="showStaffModal" @close="showStaffModal = false" max-width="4xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">
          Manage Staff Members
        </h2>
        
        <!-- Add Staff Form -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Add New Staff</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label for="staff_name" class="block text-sm font-medium text-gray-700">Full Name</label>
              <input
                type="text"
                id="staff_name"
                v-model="staffForm.name"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :class="{ 'border-red-300': staffForm.errors.name }"
              >
              <p v-if="staffForm.errors.name" class="mt-1 text-sm text-red-600">{{ staffForm.errors.name }}</p>
            </div>
            <div>
              <label for="staff_email" class="block text-sm font-medium text-gray-700">Email</label>
              <input
                type="email"
                id="staff_email"
                v-model="staffForm.email"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :class="{ 'border-red-300': staffForm.errors.email }"
              >
              <p v-if="staffForm.errors.email" class="mt-1 text-sm text-red-600">{{ staffForm.errors.email }}</p>
            </div>
            <div>
              <label for="staff_role" class="block text-sm font-medium text-gray-700">Role</label>
              <select
                id="staff_role"
                v-model="staffForm.role"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :class="{ 'border-red-300': staffForm.errors.role }"
              >
                <option value="waiter">Waiter</option>
                <option value="staff">Staff</option>
                <option value="manager">Manager</option>
              </select>
              <p v-if="staffForm.errors.role" class="mt-1 text-sm text-red-600">{{ staffForm.errors.role }}</p>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button
              type="button"
              @click="submitStaffForm"
              :disabled="staffForm.processing || isSubmitting"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            >
              <UserPlusIcon class="w-4 h-4 mr-2" />
              {{ isSubmitting ? 'Adding...' : 'Add Staff' }}
            </button>
          </div>
        </div>

        <!-- Staff List -->
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Staff Members
            </h3>
          </div>
          <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="member in staff" :key="member.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ member.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500">{{ member.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                        {{ member.role }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                      {{ member.created_at }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button
            @click="showStaffModal = false"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Close
          </button>
        </div>
      </div>
    </Modal>

    <!-- Add Staff Modal -->
    <Modal :show="showAddStaffForm" @close="showAddStaffForm = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Add New Staff Member
        </h2>
        
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              type="text"
              id="name"
              v-model="staffForm.name"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-300': staffForm.errors.name }"
              placeholder="Enter full name"
            >
            <p v-if="staffForm.errors.name" class="mt-1 text-sm text-red-600">{{ staffForm.errors.name }}</p>
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              id="email"
              v-model="staffForm.email"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-300': staffForm.errors.email }"
              placeholder="Enter email address"
            >
            <p v-if="staffForm.errors.email" class="mt-1 text-sm text-red-600">{{ staffForm.errors.email }}</p>
          </div>
          
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select
              id="role"
              v-model="staffForm.role"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="waiter">Waiter</option>
              <option value="staff">Staff</option>
              <option value="manager">Manager</option>
            </select>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            @click="showAddStaffForm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="submitStaffForm"
            :disabled="staffForm.processing || isSubmitting"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <UserPlusIcon v-if="!isSubmitting" class="w-4 h-4 mr-2" />
            {{ isSubmitting ? 'Adding...' : 'Add Staff' }}
          </button>
        </div>
      </div>
    </Modal>

    <!-- Notes Modal -->
    <Modal :show="showNotesModal" @close="showNotesModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Notes for {{ selectedAdvance?.user?.name }}'s Advance
        </h2>

        <div class="mt-4">
          <p class="text-sm text-gray-600 whitespace-pre-line">
            {{ selectedAdvance?.notes || 'No notes available.' }}
          </p>
        </div>

        <div class="flex justify-end mt-6">
          <button
            @click="showNotesModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Close
          </button>
        </div>
      </div>
    </Modal>
    </div>
  </AppLayout>
  </div>
</template>
