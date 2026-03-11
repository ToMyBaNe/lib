 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-lock mr-2 text-indigo-600"></i> Link Generator For Library Online Resources
        </h2>

        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Enter Link Here</label>
                <textarea 
                    id="baseInput"
                    class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                    rows="4"
                    placeholder="Enter link to generate link to embedd..."
                ></textarea>
            </div>

            <div class="flex gap-3">
                <button onclick="encodeBase64()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-arrow-up mr-1"></i> Generate
                </button>

                <!-- <button onclick="decodeBase64()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-arrow-down mr-1"></i> Decode
                </button> -->

                <button onclick="clearBase64()" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    <i class="fas fa-trash mr-1"></i> Clear
                </button>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Result</label>
                <textarea 
                    id="baseOutput"
                    class="w-full mt-1 p-3 border rounded-lg bg-gray-50"
                    rows="4"
                    readonly
                ></textarea>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i> Instructions
        </h2>

        <div class="space-y-3 text-sm text-gray-700">

            <p><strong>Step 1:</strong> Enter the link you want to convert.</p>

            <p><strong>Step 2:</strong> Click <span class="font-semibold text-indigo-600">Generate</span> to generate the new link.</p>

            <p><strong>Step 3:</strong> The result will appear in the <strong>Result</strong> box.</p>
            <p><strong>Step 4:</strong> Click <span class="font-semibold text-gray-600">Clear</span> if you want to clear the text fields.</p>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mt-4">
                <p class="text-yellow-800">
                    ⚠️ <strong>Note:</strong> This link will only be used to embedd in existing google site so that visitors can be able to answer our survey
                </p>
            </div>

            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-600">
                    Example:
                </p>
                <p class="font-mono text-sm mt-1">
                    https://paarl.org.ph/ → http://localhost:3000/survey?data=aHR0cHM6Ly9wYWFybC5vcmcucGgv
                </p>
            </div>

        </div>
    </div>

    <script>
        function encodeBase64(){
            const input = document.getElementById("baseInput").value;
            const encoded = btoa(input);
            const newLink = "http://localhost:3000/public?data=" + encoded;
            document.getElementById("baseOutput").value = newLink;
        }

        // function decodeBase64(){
        //     try{
        //         const input = document.getElementById("baseInput").value;
        //         const decoded = atob(input);
        //         document.getElementById("baseOutput").value = decoded;
        //     }catch(e){
        //         alert("Invalid Base64 string");
        //     }
        // }

        function clearBase64(){
            document.getElementById("baseInput").value = "";
            document.getElementById("baseOutput").value = "";
        }
    </script>

</div>

