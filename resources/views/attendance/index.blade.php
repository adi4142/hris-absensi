@extends('layouts.admin')

@section('title', 'Riwayat Absensi')
@section('page_title', 'Manajemen Kehadiran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-calendar-alt mr-2 text-primary"></i> Data Seluruh Absensi Karyawan
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm border-2">
                            <i class="fas fa-file-export mr-1"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="bg-primary" style="height: 3px; width: 100%;"></div>
                
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="m-4 alert bg-soft-success border-0 shadow-sm" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase letter-spacing-1">
                                <tr>
                                    <th class="px-4 py-3" width="60">No</th>
                                    <th class="py-3">Karyawan</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3 text-center">Waktu Masuk</th>
                                    <th class="py-3 text-center">Waktu Keluar</th>
                                    <th class="py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                <tr>
                                    <td class="px-4 font-weight-bold text-muted small">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative">
                                                @if($att->photo_in)
                                                    <img src="{{ Storage::url('attendance/' . $att->photo_in) }}" class="rounded shadow-sm border preview-img" style="width: 42px; height: 42px; object-fit: cover;" onclick="window.open(this.src)">
                                                @else
                                                    <div class="bg-soft-primary rounded border d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                        <i class="fas fa-user text-primary small"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-weight-bold text-dark small">{{ $att->employee->name }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">{{ $att->employee->nip }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark font-weight-500 small">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-soft-success font-weight-600 px-3 py-2" style="font-size: 0.75rem;">
                                            <i class="far fa-clock mr-1"></i> {{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '--:--' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-soft-danger font-weight-600 px-3 py-2" style="font-size: 0.75rem;">
                                            <i class="far fa-clock mr-1"></i> {{ $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '--:--' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            switch ($att->status) {
                                                case 'Present':
                                                    $badgeConfig = [
                                                        'class' => 'badge-soft-success',
                                                        'label' => 'Hadir'
                                                    ];
                                                    break;

                                                case 'Late':
                                                    $badgeConfig = [
                                                        'class' => 'badge-soft-warning',
                                                        'label' => 'Terlambat'
                                                    ];
                                                    break;

                                                case 'Permission':
                                                    $badgeConfig = [
                                                        'class' => 'badge-soft-info',
                                                        'label' => 'Izin'
                                                    ];
                                                    break;

                                                case 'Alpha':
                                                    $badgeConfig = [
                                                        'class' => 'badge-soft-danger',
                                                        'label' => 'Alpha'
                                                    ];
                                                    break;

                                                default:
                                                    $badgeConfig = [
                                                        'class' => 'badge-soft-secondary',
                                                        'label' => $att->status
                                                    ];
                                                    break;
                                            }
                                        @endphp

                                        <span class="badge {{ $badgeConfig['class'] }} shadow-sm px-3 py-2" style="min-width: 80px;">
                                            {{ strtoupper($badgeConfig['label']) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <img src="{{ asset('AdminLTE/dist/img/no-data.png') }}" class="mb-3 opacity-25" style="width: 120px;" onerror="this.src='https://illustrations.popsy.co/amber/no-messages.svg'">
                                            <h5 class="text-muted font-weight-light">Belum ada data absensi tercatat</h5>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="p-4 border-top bg-light">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <div class="chat-widget minimized" id="chatWidget">
    <div class="chat-toggle-btn" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </div>
    
    <div class="chat-header" onclick="toggleChat()">
        <span><i class="fas fa-robot mr-2"></i> HR Assistant</span>
        <i class="fas fa-times"></i>
    </div>
    
    <div class="chat-body" id="chatBody">
        <div class="message bot">
            Halo {{ Auth::user()->name }}! 👋<br><br>
            Saya HR Assistant, siap membantu Anda.<br><br>
        </div>
    </div>
    <div class="chat-footer">
        <input type="text" id="chatInput" placeholder="Ketik pesan..." onkeypress="handleKeyPress(event)">
        <button onclick="sendChatMessage()">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
    // Toggle chatbot visibility
function toggleChat() {
    const widget = document.getElementById('chatWidget');
    widget.classList.toggle('minimized');
    if (!widget.classList.contains('minimized')) {
        document.getElementById('chatInput').focus();
    }
}

// Handle Enter key press
function handleKeyPress(e) {
    if (e.key === 'Enter') {
        sendChatMessage();
    }
}

// Quick reply button
function quickReply(message) {
    document.getElementById('chatInput').value = message;
    sendChatMessage();
}

// Send message to chatbot
async function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;

    appendChatMessage('user', message);
    input.value = '';

    const typingId = 'typing-' + Date.now();
    appendChatMessage('bot',
        '<div class="typing-indicator"><span></span><span></span><span></span></div>',
        typingId
    );

    try {
        const response = await fetch('{{ route("chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        const typingEl = document.getElementById(typingId);
        if (typingEl) {
            typingEl.innerHTML = data.reply ?? "Tidak ada respon dari AI.";
        }

    } catch (error) {
        console.error(error);
        const typingEl = document.getElementById(typingId);
        if (typingEl) {
            typingEl.innerHTML = "⚠️ Terjadi kesalahan server.";
        }
    }
}


// Append message to chat body
function appendChatMessage(role, text, id = null) {
    const chatBody = document.getElementById('chatBody');
    const div = document.createElement('div');
    div.className = 'message ' + role;
    if (id) div.id = id;
    div.innerHTML = text;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}
</script>    
@endpush

<style>
    .bg-soft-primary { background-color: #f0fdf4; }
    .bg-soft-success { background-color: #f0fdf4; color: #16a34a; }
    .bg-soft-danger { background-color: #fef2f2; color: #dc2626; }
    .badge-soft-success { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-soft-info { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-danger { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
    
    .preview-img { cursor: pointer; transition: transform 0.2s; }
    .preview-img:hover { transform: scale(1.1); z-index: 10; position: relative; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .font-weight-500 { font-weight: 500; }
    .font-weight-600 { font-weight: 600; }
    .border-2 { border-width: 2px !important; }
</style>
@endsection


