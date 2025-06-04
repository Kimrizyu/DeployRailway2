<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ruang Talenta</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .hero-section {
      position: relative;
      height: 100vh;
      background-image: url("{{ asset('images/Group 427319046 (1).png') }}");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: white;
    }

    .stat-box {
      text-align: center;
      margin: 0 20px;
    }
    .category-card {
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .category-card:hover {
        transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-light">
@include('layouts.navigation')

<!-- Hero Section -->
<section class="hero-section d-flex align-items-center">
  <div class="hero-overlay d-flex flex-row justify-content-between align-items-center w-100 px-5">
    <div class="text-start" style="max-width: 600px;">
      <h1 class="display-5 fw-bold">Bangun Karier Impianmu di <span style="color: #222D65;">Ruang Talenta</span></h1>
      <p class="lead mt-3">Jelajahi ribuan lowongan kerja yang sesuai dengan keahlian dan passionmu</p>

      <div class="input-group mt-4">
        <input type="text" class="form-control rounded-start" placeholder="Posisi atau perusahaan">
        <button class="btn btn-danger rounded-end px-4">Cari</button>
      </div>

      <div class="d-flex mt-5 gap-5 text-white">
        <div class="stat-box">
          <h3>10K+</h3>
          <p>Lowongan Tersedia</p>
        </div>
        <div class="stat-box">
          <h3>1K+</h3>
          <p>Perusahaan</p>
        </div>
        <div class="stat-box">
          <h3>3K+</h3>
          <p>Blog & Forum</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold mb-4">Popular Category</h2>
    <div class="row justify-content-center g-4">
      @foreach ($categories as $index => $category)
        <div class="col-md-2 col-6 text-center">
            <a href="{{ url('/filterjob') }}?categories[]={{ $category->id }}" style="text-decoration: none; color: inherit;">
                <div class="category-card">
                    <img src="{{ $categoryImages[$index] ?? 'https://via.placeholder.com/80' }}" alt="{{ $category->name }}" class="mb-2" width="180" height="250">
                    <p>{{ $category->name }}</p>
                </div>
            </a>
        </div>  
      @endforeach
    </div>
  </div>
</section>

<!-- Jobs Section -->
<section class="py-3">
  <div class="container">
    <h2 class="fw-bold mb-4">New Job Offer</h2>
    <div class="row g-4">
      @forelse ($jobs as $job)
      @php
        $needsResume = in_array('Resume', $job->selection_methods ?? []);
        $needsLetter = in_array('Application Letter', $job->selection_methods ?? []);
        $needsPortofolio = in_array('Portofolio', $job->selection_methods ?? []);
      @endphp
      <div class="col-md-4">
        <div class="card h-100 shadow-sm rounded-4 p-2">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div class="d-flex align-items-center">
                <img src="{{ asset('storage/' . optional($job->company?->profile)->avatar ?? 'default-avatar.png') }}"
                  onerror="this.src='https://via.placeholder.com/40'"
                  alt="Logo"
                  width="40"
                  class="me-2 rounded-circle">
                <div>
                  <strong class="d-block">{{ $job->company->profile->full_name ?? 'Unknown' }}</strong>
                  <small class="text-muted">{{ $job->location ?? 'Example Location' }}</small>
                </div>
              </div>
              <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
            </div>

            <span class="badge bg-warning text-dark mb-2">{{ $job->category->name ?? 'Uncategorized' }}</span>

            <h5 class="fw-bold mb-2">{{ $job->title }}</h5>

            <div class="d-flex flex-wrap gap-2 mb-3">
              @foreach ($job->skills as $skill)
              <span class="badge bg-primary">{{ $skill }}</span>
              @endforeach
            </div>

            <strong class="fs-6 text-dark">
              Rp {{ number_format($job->salary_min, 0, ',', '.') }} - Rp {{ number_format($job->salary_max, 0, ',', '.') }}
            </strong>

            @if (auth()->check() && auth()->user()->hasRole('pencarikerja'))
              @if ($needsResume || $needsLetter || $needsPortofolio)
              <button 
                type="button" 
                class="btn btn-primary w-100 mt-3" 
                onclick="openApplyModal({{ $job->id }}, {{ json_encode($needsResume) }}, {{ json_encode($needsLetter) }}, {{ json_encode($needsPortofolio) }})">
                Apply
              </button>
              @else
              <form action="{{ route('job.apply', $job->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary w-100 mt-3">Apply</button>
              </form>
              @endif
            @endif
          </div>
        </div>
      </div>
      @empty
      <p class="text-muted text-center">No job postings available.</p>
      @endforelse
    </div>

    <div class="text-center mt-4">
      <a href="{{ route('job.show') }}" class="btn btn-primary px-4 py-2">View All Jobs</a>
    </div>
  </div>
</section>
@if(isset($job))
  <!-- Modal Apply -->
  <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="applyForm" action="{{ route('job.apply', $job->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Lengkapi Berkas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="job_id" id="modalJobId">

            <!-- Resume Upload -->
            <div id="resumeSection" class="mb-3 d-none">
              <label class="form-label">Unggah Resume (PDF)</label>
              <input type="file" name="resume_file" accept="application/pdf" class="form-control">
            </div>

            <!-- Application Letter Upload -->
            <div id="letterSection" class="mb-3 d-none">
              <label class="form-label">Unggah Application Letter (PDF)</label>
              <input type="file" name="application_letter_file" accept="application/pdf" class="form-control">
            </div>

            <!-- Portofolio Link -->
            <div id="portofolioSection" class="mb-3 d-none">
              <label class="form-label">Tautan Portofolio (URL)</label>
              <input type="url" name="portofolio_link" class="form-control" placeholder="https://portofolio.com/nama-anda">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Kirim Lamaran</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endif

<!-- Footer -->
<footer class="text-white mt-5 pt-4 pb-3" style="background-color: #222D65;">
  <div class="container">
    <div class="row text-center text-md-start">
      <div class="col-md-4 mb-4">
        <div class="d-flex align-items-center mb-2">
          <img src="https://figmage.com/images/MxRP4yFlcG5FSL9xhYLr8.png" alt="Logo" width="40" class="me-2">
          <h5 class="mb-0 fw-bold">Ruang Talenta</h5>
        </div>
        <p class="small">Ruang Talenta membantu Anda terhubung dengan peluang kerja dan pengembangan skill.</p>
      </div>

      <div class="col-md-4 mb-4">
        <h6 class="fw-bold mb-3">Menu</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-white text-decoration-none">Home</a></li>
          <li><a href="#" class="text-white text-decoration-none">Find a Job</a></li>
          <li><a href="#" class="text-white text-decoration-none">Dashboard</a></li>
          <li><a href="#" class="text-white text-decoration-none">Forum & Community</a></li>
        </ul>
      </div>

      <div class="col-md-4 mb-4">
        <h6 class="fw-bold mb-3">Media Sosial</h6>
        <div class="d-flex justify-content-center justify-content-md-start gap-3">
          <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white fs-5"><i class="bi bi-x"></i></a>
          <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white fs-5"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
          <a href="#" class="text-white fs-5"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>
    </div>

    <hr class="border-light">
    <div class="text-center small mt-3">
      Hak cipta &copy; 2025 RuangTalenta
    </div>
  </div>
</footer>

@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    confirmButtonColor: '#3085d6'
  });
</script>
@endif

<script>
function openApplyModal(jobId, needsResume, needsLetter, needsPortofolio) {
  const modal = new bootstrap.Modal(document.getElementById('applyModal'));
  const form = document.getElementById('applyForm');

  form.action = `/job/${jobId}/apply`;
  document.getElementById('modalJobId').value = jobId;

  document.getElementById('resumeSection').classList.toggle('d-none', !needsResume);
  document.getElementById('letterSection').classList.toggle('d-none', !needsLetter);
  document.getElementById('portofolioSection').classList.toggle('d-none', !needsPortofolio);

  modal.show();
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
