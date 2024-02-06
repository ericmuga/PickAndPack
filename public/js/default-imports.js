import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
// import Button from 'primevue/button';
// import MultiSelect from 'primevue/multiselect';
// import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
// import Swal from 'sweetalert2'
// import FilterPane from '@/Components/FilterPane.vue'
import Modal from '@/Components/Modal.vue'
