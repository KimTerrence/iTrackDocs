<div class="animate-fade-in-up space-y-6">
    
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Create Document</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Fill in the details below to generate a new tracking ID and routing slip.</p>
            </div>
        </div>
    </div>

    <form action="index.php?action=store" method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <!-- Document Information Section -->
        <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-xl ring-1 ring-blue-200 dark:ring-blue-500/20">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Document Information</h2>
            </div>

            <div class="space-y-5">
                <!-- Title Field -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Document Title / Subject
                    </label>
                    <input type="text" name="title" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-600"
                        placeholder="Enter the document title or subject">
                    <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Be specific and descriptive for easier tracking.
                    </p>
                </div>

                <!-- Category Field -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Document Category
                    </label>
                    <div class="relative">
                        <select name="category" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>Select category...</option>
                            <option value="IC">IC - Internal Communication</option>
                            <option value="EC">EC - External Communication</option>
                        </select>
                        <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachment Section -->
        <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-xl ring-1 ring-amber-200 dark:ring-amber-500/20">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Attachment</h2>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Scanned Copy <span class="font-normal text-slate-500">(Optional)</span>
                </label>
                <input type="file" name="scanned_file" accept=".pdf,.jpg,.jpeg,.png" 
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-3 outline-none transition-all
                           file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-100 file:text-brand-700 hover:file:bg-brand-200 cursor-pointer">
                <div class="mt-3 flex items-start gap-2">
                    <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-xs text-slate-400">
                        Accepted formats: <span class="font-semibold text-slate-500">PDF, JPG, PNG</span> with maximum file size of <span class="font-semibold text-slate-500">5MB</span>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                <a href="index.php?action=dashboard" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-brand-600 hover:bg-brand-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-500/30 transition-all active:scale-[0.98]">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Create Document
                </button>
            </div>
        </div>

    </form>
</div>
