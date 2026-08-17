<dialog id="modalDisposisi" class="p-0 rounded-xl border border-borderline bg-surface w-full max-w-md shadow-sm">
    <div class="p-8">
        <h3 class="font-serif text-2xl mb-6">Buat Disposisi</h3>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                <ul class="list-disc pl-4 flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('disposisi.store') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Pilih Surat Masuk</label>
                <select name="surat_masuk_id" class="input-base" required>
                    <option value="" disabled selected>-- Pilih Surat --</option>
                    @foreach($suratMasuk as $sm)
                        <option value="{{ $sm->id }}" {{ old('surat_masuk_id') == $sm->id ? 'selected' : '' }}>
                            {{ $sm->nomor_surat }} - {{ Str::limit($sm->perihal, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Diteruskan Ke</label>
                    <select name="tujuan" class="input-base" required>
                        <option value="" disabled {{ old('tujuan') ? '' : 'selected' }}>-- Pilih Kasi --</option>
                        <option value="Kasi Kesejahteraan Sosial" {{ old('tujuan') == 'Kasi Kesejahteraan Sosial' ? 'selected' : '' }}>Kasi Kesejahteraan Sosial</option>
                        <option value="Kasi Ekonomi dan Pembangunan" {{ old('tujuan') == 'Kasi Ekonomi dan Pembangunan' ? 'selected' : '' }}>Kasi Ekonomi dan Pembangunan</option>
                        <option value="Kasi Pemerintahan" {{ old('tujuan') == 'Kasi Pemerintahan' ? 'selected' : '' }}>Kasi Pemerintahan</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Sifat</label>
                    <select name="sifat" class="input-base" required>
                        <option value="Biasa" {{ old('sifat') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Penting" {{ old('sifat') == 'Penting' ? 'selected' : '' }}>Penting</option>
                        <option value="Segera" {{ old('sifat') == 'Segera' ? 'selected' : '' }}>Segera</option>
                        <option value="Rahasia" {{ old('sifat') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Isi Instruksi / Catatan</label>
                <textarea name="isi_disposisi" class="input-base min-h-[100px] resize-y" placeholder="Cth: Tolong segera diproses..." required>{{ old('isi_disposisi') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modalDisposisi').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById('modalDisposisi').showModal();
    });
</script>
@endif