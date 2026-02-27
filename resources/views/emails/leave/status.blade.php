@component('mail::message')
# Halo {{ $leaveRequest->user->name }},

Pengajuan cuti Anda telah diproses oleh HRD. Berikut ini adalah detail dan status akhirnya:

@component('mail::panel')
**Periode Cuti:** {{ \Carbon\Carbon::parse($leaveRequest->start_date)->isoFormat('D MMMM YYYY') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->isoFormat('D MMMM YYYY') }}  
**Durasi:** {{ $leaveRequest->days }} Hari  
**Alasan:** {{ $leaveRequest->reason }}  
**Status Pengajuan:** @if($leaveRequest->status === 'APPROVED') <span style="color: green; font-weight: bold;">DISETUJUI</span> @else <span style="color: red; font-weight: bold;">DITOLAK</span> @endif
@endcomponent

@if($leaveRequest->hrd_note)
**Catatan HRD:**
<br/>
<em style="color: #6c757d;">"{{ $leaveRequest->hrd_note }}"</em>
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
