<!-- Settings Page Content -->

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Settings Menu -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <nav class="flex flex-col">
                <button onclick="switchTab('general')" class="settings-tab active px-6 py-3 text-left border-l-4 border-indigo-600 bg-indigo-50" data-tab="general">
                    <i class="fas fa-cog mr-2"></i> General Settings
                </button>
                <button onclick="switchTab('email')" class="settings-tab px-6 py-3 text-left border-l-4 border-transparent hover:bg-gray-50" data-tab="email">
                    <i class="fas fa-envelope mr-2"></i> Email Settings
                </button>
                <button onclick="switchTab('survey')" class="settings-tab px-6 py-3 text-left border-l-4 border-transparent hover:bg-gray-50" data-tab="survey">
                    <i class="fas fa-poll mr-2"></i> Survey Settings
                </button>
                <button onclick="switchTab('advanced')" class="settings-tab px-6 py-3 text-left border-l-4 border-transparent hover:bg-gray-50" data-tab="advanced">
                    <i class="fas fa-sliders-h mr-2"></i> Advanced
                </button>
                <button onclick="switchTab('account')" class="settings-tab px-6 py-3 text-left border-l-4 border-transparent hover:bg-gray-50" data-tab="account">
                    <i class="fas fa-user mr-2"></i> Account
                </button>
            </nav>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="lg:col-span-2">
        <!-- General Settings Tab -->
        <div id="tab-general" class="settings-content">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">General Settings</h2>
                
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Title</label>
                        <input type="text" value="Library Survey System" class="form-input">
                        <p class="text-sm text-gray-500 mt-1">The name of your survey application</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                        <textarea rows="3" class="form-input" placeholder="Enter site description"></textarea>
                        <p class="text-sm text-gray-500 mt-1">Brief description of your survey</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Survey Page URL</label>
                        <input type="url" value="http://localhost/survey/" class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Public URL of your survey form</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Enable email capture</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Settings Tab -->
        <div id="tab-email" class="settings-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Email Settings</h2>
                
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Server</label>
                        <input type="text" placeholder="smtp.gmail.com" class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                            <input type="number" placeholder="587" value="587" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                            <input type="email" placeholder="your-email@gmail.com" class="form-input">
                        </div>
                    </div>

                    <div class="pt-6 border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Save Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Survey Settings Tab -->
        <div id="tab-survey" class="settings-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Survey Settings</h2>
                
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Survey Title</label>
                        <input type="text" placeholder="Library Usage Survey" class="form-input">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Survey Description</label>
                        <textarea rows="3" placeholder="Help us improve..." class="form-input"></textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Show question numbers</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Show progress bar</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Save Survey Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="tab-advanced" class="settings-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Advanced Settings</h2>
                
                <div class="space-y-6">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            These settings affect system behavior. Only change if you know what you're doing.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Database</h3>
                        <p class="text-sm text-gray-600 mb-3">Database: library_survey</p>
                        <button class="btn btn-secondary">
                            <i class="fas fa-redo mr-2"></i> Verify Connection
                        </button>
                    </div>

                    <div class="pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance</h3>
                        <div class="space-y-2">
                            <button class="block w-full text-left btn btn-secondary">
                                <i class="fas fa-trash mr-2"></i> Clear Cache
                            </button>
                            <button class="block w-full text-left btn btn-secondary">
                                <i class="fas fa-download mr-2"></i> Generate Backup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings Tab -->
        <div id="tab-account" class="settings-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Account Settings</h2>
                
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>" disabled class="form-input opacity-50">
                    </div>

                    <div class="pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" class="form-input" placeholder="••••••••">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" class="form-input" placeholder="••••••••">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" class="form-input" placeholder="••••••••">
                            </div>

                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </div>

                    <div class="pt-6 border-t">
                        <a href="login.php?action=logout" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-content').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.classList.remove('active', 'border-indigo-600', 'bg-indigo-50');
            btn.classList.add('border-transparent');
        });
        
        // Show selected tab
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Add active class to button
        event.target.closest('.settings-tab').classList.add('active', 'border-indigo-600', 'bg-indigo-50');
        event.target.closest('.settings-tab').classList.remove('border-transparent');
    }
</script>
