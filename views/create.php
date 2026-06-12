<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Create Document</h1>
        <p class="text-slate-500 dark:text-slate-400">Generate a new tracking ID and routing slip.</p>
    </div>

    <form action="index.php?action=store" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
        <div class="space-y-6">
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Document Title/Subject</label>
                <input type="text" name="title" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Originating Department</label>
                <input type="text" name="origin" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Scanned Copy (Optional)</label>
                <input type="file" name="scanned_file" accept=".pdf,.jpg,.jpeg,.png" 
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-3 py-2.5 outline-none transition-all
                           file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-100 file:text-brand-700 hover:file:bg-brand-200 cursor-pointer">
                <p class="text-xs text-slate-400 mt-2">Accepted formats: PDF, JPG, PNG (Max 5MB).</p>
            </div>

        </div>
        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all">
                Generate Routing Slip
            </button>
        </div>
    </form>
</div>