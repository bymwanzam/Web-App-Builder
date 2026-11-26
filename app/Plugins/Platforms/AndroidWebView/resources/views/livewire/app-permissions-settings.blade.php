<div>
<form wire:submit.prevent="save">

    <!-- Location Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Location Permission</h3>
                <p class="settings-card-description">Allow the app to access device location services</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_location"
            :live="true"
            label="Enable location access"
        />
        <small class="helper-text">When enabled, the app will request permission to access the user's location</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Get current location using AppBridge
AppBridge.getLocation()
    .then(result => {
        console.log(\`Location: \${result.latitude}, \${result.longitude}\`);
        console.log(\`Accuracy: \${result.accuracy}m\`);
    })
    .catch(err => {
        console.error('Location error:', err.message);
    });</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">AppBridge.getLocation()</code> which returns a Promise with latitude, longitude, accuracy, altitude, speed, and bearing.
                </p>
            </div>
        </div>
    </div>

    <!-- Camera Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Camera Permission</h3>
                <p class="settings-card-description">Allow the app to access device camera</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_camera"
            :live="true"
            label="Enable camera access"
        />
        <small class="helper-text">When enabled, the app will request permission to access the device camera</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Take a photo using AppBridge
AppBridge.takePhoto()
    .then(result => {
        const img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + result.data;
        document.body.appendChild(img);
    })
    .catch(err => {
        console.error('Camera error:', err.message);
    });</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">AppBridge.takePhoto()</code> which returns a Promise with base64-encoded image data.
                </p>
            </div>
        </div>
    </div>

    <!-- Storage Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                    <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                    <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Storage Permission</h3>
                <p class="settings-card-description">Allow the app to read and write files on device storage</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_storage"
            :live="true"
            label="Enable storage access"
        />
        <small class="helper-text">When enabled, the app will request permission to access photos, videos, and files</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Pick image from gallery using AppBridge
AppBridge.pickImage()
    .then(result => {
        const img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + result.data;
        document.body.appendChild(img);
    })
    .catch(err => {
        console.error('Gallery error:', err.message);
    });</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">AppBridge.pickImage()</code> which opens the gallery picker and returns base64-encoded image data.
                </p>
            </div>
        </div>
    </div>

    <!-- Record Audio Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Microphone Permission</h3>
                <p class="settings-card-description">Allow the app to record audio using the device microphone</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_record_audio"
            :live="true"
            label="Enable microphone access"
        />
        <small class="helper-text">When enabled, the app will request permission to record audio</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Record audio using WebRTC
navigator.mediaDevices.getUserMedia({ audio: true })
    .then(stream => {
        const recorder = new MediaRecorder(stream);
        recorder.ondataavailable = (e) => {
            console.log('Audio data:', e.data);
        };
        recorder.start();
        setTimeout(() => recorder.stop(), 5000); // Stop after 5s
    })
    .catch(err => console.error('Microphone error:', err));</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses standard <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">getUserMedia()</code> API. Works automatically when permission is enabled.
                </p>
            </div>
        </div>
    </div>

    <!-- Read Contacts Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Contacts Permission</h3>
                <p class="settings-card-description">Allow the app to access device contacts</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_read_contacts"
            :live="true"
            label="Enable contacts access"
        />
        <small class="helper-text">When enabled, the app will request permission to read contact information</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Get all contacts using AppBridge
AppBridge.getContacts()
    .then(result => {
        result.contacts.forEach(contact => {
            console.log(\`\${contact.name}: \${contact.phone}\`);
        });
    })
    .catch(err => {
        console.error('Contacts error:', err.message);
    });</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">AppBridge.getContacts()</code> which returns a Promise with an array of contact objects.
                </p>
            </div>
        </div>
    </div>

    <!-- Vibrate Permission Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Vibrate Permission</h3>
                <p class="settings-card-description">Allow the app to control device vibration</p>
            </div>
        </div>

        <x-toggle
            wire-model="permission_vibrate"
            :live="true"
            label="Enable vibration control"
        />
        <small class="helper-text">When enabled, the app will be able to trigger device vibration for haptic feedback</small>

        <!-- Code Example -->
        <div x-data="{ open: false }" class="mt-3">
            <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg x-show="!open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="open" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span x-text="open ? 'Hide code example' : 'Show code example'"></span>
            </button>

            <div x-show="open" x-collapse class="mt-2 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JavaScript Example:</p>
                <pre class="text-xs bg-gray-900 text-green-400 p-3 rounded overflow-x-auto"><code>// Vibrate device using AppBridge
AppBridge.vibrate(200); // Vibrate for 200ms

// Show a toast notification
AppBridge.toast('Vibration triggered!');</code></pre>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    ℹ️ Uses <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">AppBridge.vibrate(ms)</code> for haptic feedback. Duration is in milliseconds.
                </p>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>
</div>
