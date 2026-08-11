<dialog id="modalPreviewDokumen" class="p-0 rounded-xl border border-borderline bg-surface w-[min(1100px,94vw)] max-w-none shadow-xl">
    <div class="flex flex-col max-h-[92vh]">
        <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-borderline">
            <div class="min-w-0">
                <h3 id="previewDokumenTitle" class="font-serif text-xl truncate">Pratinjau Dokumen</h3>
                <p class="text-xs text-muted mt-1">PDF dan gambar ditampilkan langsung tanpa bergantung pada public/storage.</p>
            </div>
            <button type="button" onclick="closeDocumentPreview()" class="w-9 h-9 rounded-md border border-borderline hover:bg-canvas flex items-center justify-center shrink-0" aria-label="Tutup pratinjau">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>

        <div class="bg-[#EFEDEA] flex-1 min-h-[420px] overflow-auto p-4">
            <img id="previewDokumenImage" src="" alt="Pratinjau gambar dokumen" class="hidden mx-auto max-w-full h-auto rounded-md shadow-sm bg-white">
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

        image.classList.add('hidden');
        pdf.classList.add('hidden');
        image.removeAttribute('src');
        pdf.removeAttribute('src');
        titleEl.textContent = title || 'Pratinjau Dokumen';

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
</script>
<?php /**PATH /var/www/resources/views/surat/modals/preview_dokumen.blade.php ENDPATH**/ ?>