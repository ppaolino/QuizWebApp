<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div class="input-icon">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <i class="fas fa-user"></i>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4 input-icon">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <i class="fas fa-envelope"></i>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 input-icon">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <i class="fas fa-lock"></i>
            <span class="password-toggle" onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
            </span>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 input-icon">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <i class="fas fa-lock"></i>
            <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                <i class="fas fa-eye"></i>
            </span>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register as Creator/Admin -->
        <div class="block mt-4">
            <label class="text-sm font-medium text-gray-700">Register as:</label>

            <div class="role-option mt-2">
                <label for="role_creator" class="inline-flex items-center">
                    <input id="role_creator" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="role"
                        value="creator">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Register as creator') }}</span>
                </label>
            </div>

            <div class="role-option mt-2">
                <label for="role_admin" class="inline-flex items-center">
                    <input id="role_admin" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="role"
                        value="admin">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Register as admin') }}</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button type="button" class="ms-4" id="registerBtn">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Admin Security Code Modal (Bootstrap 5 structure) -->
    <div class="modal fade" id="adminCodeModal" tabindex="-1" aria-labelledby="adminCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminCodeModalLabel">{{ __('Admin Security Code') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <label for="admin_security_code"
                        class="form-label">{{ __('Enter the admin security code:') }}</label>
                    <input type="password" class="form-control" id="admin_security_code" autocomplete="off">
                    <div id="adminCodeError" class="text-danger mt-2" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="submitAdminCode">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle password visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = field.parentElement.querySelector('.password-toggle i');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Mutually exclusive checkboxes
            $('#role_creator').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#role_admin').prop('checked', false);
                }
            });
            $('#role_admin').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#role_creator').prop('checked', false);
                }
            });

            // Intercept register button click
            $('#registerBtn').on('click', function(e) {
                if ($('#role_admin').is(':checked')) {
                    e.preventDefault();
                    $('#admin_security_code').val('');
                    $('#adminCodeError').hide();
                    var modal = new bootstrap.Modal(document.getElementById('adminCodeModal'));
                    modal.show();
                } else {
                    $('#registerForm').submit();
                }
            });

            // Handle admin code modal confirm
            $('#submitAdminCode').on('click', function() {
                var code = $('#admin_security_code').val();
                // Replace 'ADMIN123' with your actual code
                if (code === 'ADMIN123') {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('adminCodeModal'));
                    modal.hide();
                    $('#registerForm').submit();
                } else {
                    $('#adminCodeError').text('{{ __('Invalid security code.') }}').show();
                }
            });

            // Allow pressing Enter in the admin code field
            $('#admin_security_code').on('keypress', function(e) {
                if (e.key === 'Enter') {
                    $('#submitAdminCode').click();
                }
            });
        </script>
    @endpush
</x-guest-layout>
