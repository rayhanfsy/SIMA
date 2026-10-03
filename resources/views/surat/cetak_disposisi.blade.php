<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Disposisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            vertical-align: top;
        }
        .text-bold {
            font-weight: bold;
        }
        .no-border-bottom {
            border-bottom: none;
        }
        .no-border-top {
            border-top: none;
        }
        .col-half {
            width: 50%;
        }
        .footer-note {
            margin-top: 10px;
            font-size: 12px;
        }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <h2>KARTU PENERUS - DISPOSISI</h2>
        
        @php
            $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $bulan = $bulanRomawi[\Carbon\Carbon::now()->format('n') - 1];
            $tahun = \Carbon\Carbon::now()->format('Y');
            $noUrut = $disposisi->suratMasuk ? str_pad($disposisi->suratMasuk->nomor_urut, 2, '0', STR_PAD_LEFT) : '00';
            $kode = "149/{$noUrut}-KEL.DCR/{$bulan}/{$tahun}";
            
            $tglPenyelesaian = $disposisi->suratMasuk ? \Carbon\Carbon::parse($disposisi->suratMasuk->tanggal_diterima)->format('d-m-Y') : '-';
        @endphp

        <table>
            <tr>
                <td class="col-half">
                    <span class="text-bold">INDEK/KODE</span> &nbsp;&nbsp;&nbsp;&nbsp; {{ $kode }}
                </td>
                <td class="col-half">
                    <span class="text-bold">Tanggal Penyelesaian</span><br><br>
                    {{ $tglPenyelesaian }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="width:100%; border:none;">
                        <tr>
                            <td style="border:none; width:100px; padding:5px 0;">Perihal</td>
                            <td style="border:none; width:10px; padding:5px 0;">:</td>
                            <td style="border:none; padding:5px 0;">{{ $disposisi->suratMasuk->perihal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border:none; padding:5px 0;">Tgl/No</td>
                            <td style="border:none; padding:5px 0;">:</td>
                            <td style="border:none; padding:5px 0;">{{ $disposisi->tgl_no }}</td>
                        </tr>
                        <tr>
                            <td style="border:none; padding:5px 0;">Asal</td>
                            <td style="border:none; padding:5px 0;">:</td>
                            <td style="border:none; padding:5px 0;">{{ $disposisi->suratMasuk->pengirim ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-half" style="height: 250px;">
                    <span class="text-bold">INSTRUKSI/INFORMASI *)</span><br><br>
                    {!! nl2br(e($disposisi->isi_disposisi)) !!}
                </td>
                <td class="col-half">
                    <span class="text-bold">Diteruskan Kepada :</span><br><br>
                    <table style="width:100%; border:none;">
                        @php
                            $tujuanList = [
                                'Sekretaris Lurah',
                                'Kasi Pemerintahan',
                                'Kasi Kesejahteraan Sosial',
                                'Kasi Ekonomi dan Pembangunan'
                            ];
                        @endphp
                        @foreach($tujuanList as $index => $tujuanItem)
                        <tr>
                            <td style="border:none; width:20px; padding:5px 0;">{{ $index + 1 }}.</td>
                            <td style="border:none; padding:5px 0;">
                                {{ $tujuanItem }} 
                                @if($disposisi->tujuan === $tujuanItem)
                                    <span style="float:right; font-weight:bold;">(&#10003;)</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="border:none; width:20px; padding:5px 0;">5.</td>
                            <td style="border:none; padding:5px 0;">
                                {{ $disposisi->tujuan_tambahan ?: '________________________' }}
                                @if(!empty($disposisi->tujuan_tambahan))
                                    <span style="float:right; font-weight:bold;">(&#10003;)</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            *) 1. Kepada bawahan "Instruksi" dan atau "Informasi"<br>
            &nbsp;&nbsp;&nbsp;2. Kepada atasan "Informasi", coret instruksi
        </div>
    </div>
</body>
</html>
