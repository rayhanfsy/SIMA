<dialog id="modalPreviewDokumen" class="p-0 rounded-xl border border-borderline bg-surface w-[min(1100px,94vw)] max-w-none shadow-xl">
    <div class="flex flex-col max-h-[92vh]">
        <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-borderline">
            <div class="min-w-0 flex-1">
                <h3 id="previewDokumenTitle" class="font-serif text-xl truncate">Pratinjau Dokumen</h3>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="previewDokumenOpenTab" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-borderline rounded-md hover:bg-canvas text-ink transition-colors" title="Buka di tab baru">
                    <i class="ph ph-arrow-square-out text-sm"></i> Tab Baru
                </a>
                <a id="previewDokumenDownload" href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-borderline rounded-md hover:bg-canvas text-ink transition-colors" title="Unduh berkas">
                    <i class="ph ph-download-simple text-sm"></i> Unduh
                </a>
                <button type="button" onclick="closeDocumentPreview()" class="w-9 h-9 rounded-md border border-borderline hover:bg-canvas flex items-center justify-center text-muted hover:text-ink transition-colors" aria-label="Tutup pratinjau">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>
        </div>

        <div class="bg-[#EFEDEA] flex-1 min-h-[420px] overflow-auto p-4 flex items-center justify-center">
            <img id="previewDokumenImage" src="" alt="Pratinjau gambar dokumen" class="hidden mx-auto max-w-full max-h-[75vh] h-auto object-contain rounded-md shadow-sm bg-white">
            <iframe id="previewDokumenPdf" src="" title="Pratinjau PDF" class="hidden w-full h-[72vh] rounded-md bg-white border-0"></iframe>
        </div>
    </div>
</dialog>

<script>
    function openDocumentPreview(url, type, title) {
        const modal = document.getElementById('modalPreviewDokumen');
        const image = document.getElementById('previewDokumenImage');
        const pdf = document.getElementById('previewDokumenPdf');
        const titleEl = document.getElementById('previewDokumenTitle');
        const tabLink = document.getElementById('previewDokumenOpenTab');
        const dlLink = document.getElementById('previewDokumenDownload');

        image.classList.add('hidden');
        pdf.classList.add('hidden');
        image.removeAttribute('src');
        pdf.removeAttribute('src');
        titleEl.textContent = title || 'Pratinjau Dokumen';

        tabLink.href = url;
        dlLink.href = url + (url.includes('?') ? '&' : '?') + 'download=1';

        if (type === 'image') {
            image.src = url;
            image.classList.remove('hidden');
        } else {
            pdf.src = url;
            pdf.classList.remove('hidden');
        }

        modal.showModal();
    }

    function closeDocumentPreview() {
        const modal = document.getElementById('modalPreviewDokumen');
        const image = document.getElementById('previewDokumenImage');
        const pdf = document.getElementById('previewDokumenPdf');

        image.removeAttribute('src');
        pdf.removeAttribute('src');
        modal.close();
    }

    document.getElementById('modalPreviewDokumen')?.addEventListener('click', function(e) {
        if (e.target === this) closeDocumentPreview();
    });
</script>
