@extends('layouts.app')

@section('title', 'Master Data Akademik')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Master Data Akademik</span>
    </div>
    <div class="page-title">Master Data Akademik</div>
    <div class="page-subtitle">Kelola tahun ajaran, tingkat kelas, mata pelajaran, dan KKM</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('classrooms.index') }}" class="btn btn-outline">
      <i class="ti ti-door"></i> Kelola Kelas
    </a>
    @switch($activeTab)
      @case('tahun-ajaran')
        <button class="btn btn-primary" onclick="openModal('modalAddSchoolYear')">
          <i class="ti ti-plus"></i> Tambah Tahun Ajaran
        </button>
        @break
      @case('tingkat-kelas')
        <button class="btn btn-primary" onclick="openModal('modalAddGradeLevel')">
          <i class="ti ti-plus"></i> Tambah Tingkat
        </button>
        @break
      @case('mata-pelajaran')
        <button class="btn btn-primary" onclick="openModal('modalAddSubject')">
          <i class="ti ti-plus"></i> Tambah Mapel
        </button>
        @break
      @case('kkm')
        <button class="btn btn-primary" onclick="openModal('modalAddKkm')">
          <i class="ti ti-plus"></i> Tambah KKM
        </button>
        @break
    @endswitch
  </div>
</div>

{{-- TABS --}}
<div class="tabs-wrap">
  <a href="{{ route('master.index', ['tab' => 'tahun-ajaran']) }}"
     class="tab-item {{ $activeTab === 'tahun-ajaran' ? 'active' : '' }}">
    <i class="ti ti-calendar"></i> Tahun Ajaran
    <span class="tab-count">{{ $schoolYears->total() }}</span>
  </a>
  <a href="{{ route('master.index', ['tab' => 'tingkat-kelas']) }}"
     class="tab-item {{ $activeTab === 'tingkat-kelas' ? 'active' : '' }}">
    <i class="ti ti-stairs"></i> Tingkat Kelas
    <span class="tab-count">{{ $gradeLevels->total() }}</span>
  </a>
  <a href="{{ route('master.index', ['tab' => 'mata-pelajaran']) }}"
     class="tab-item {{ $activeTab === 'mata-pelajaran' ? 'active' : '' }}">
    <i class="ti ti-book"></i> Mata Pelajaran
    <span class="tab-count">{{ $subjects->total() }}</span>
  </a>
  <a href="{{ route('master.index', ['tab' => 'kkm']) }}"
     class="tab-item {{ $activeTab === 'kkm' ? 'active' : '' }}">
    <i class="ti ti-target"></i> KKM
    <span class="tab-count">{{ $kkms->total() }}</span>
  </a>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- TAB: TAHUN AJARAN --}}
{{-- ══════════════════════════════════════════════ --}}
@if($activeTab === 'tahun-ajaran')
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Tahun Ajaran</th>
          <th>Kurikulum</th>
          <th>Semester</th>
          <th>Periode</th>
          <th>Status</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schoolYears as $sy)
          <tr>
            <td><strong>{{ $sy->year }}</strong></td>
            <td>{{ $sy->curriculum->name }}</td>
            <td>Semester {{ $sy->semester }}</td>
            <td style="font-size:11px;">
              {{ format_date($sy->start_date) }} — {{ format_date($sy->end_date) }}
            </td>
            <td>
              @if($sy->is_active)
                <span class="badge badge-green">Aktif</span>
              @else
                <span class="badge badge-amber">Tidak Aktif</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                @if(!$sy->is_active)
                  <form method="POST" action="{{ route('master.school-years.set-active', hid($sy)) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline" title="Jadikan Aktif">
                      <i class="ti ti-check" style="font-size:12px;"></i> Set Aktif
                    </button>
                  </form>
                @endif
                @php $syPayload = $sy->only(['curriculum_id', 'year', 'semester', 'start_date', 'end_date']); @endphp
                <button class="btn btn-sm btn-icon btn-outline" title="Edit"
                        onclick='openEditSchoolYearModal(@json($syPayload), "{{ hid($sy) }}")'>
                  <i class="ti ti-edit" style="font-size:13px;"></i>
                </button>
                @if(!$sy->is_active)
                  <button class="btn btn-sm btn-icon btn-danger" title="Hapus"
                          onclick="openDeleteModal('formDeleteSY{{ $sy->id }}')">
                    <i class="ti ti-trash" style="font-size:13px;"></i>
                  </button>
                  <form id="formDeleteSY{{ $sy->id }}" method="POST"
                        action="{{ route('master.school-years.destroy', hid($sy)) }}" style="display:none;">
                    @csrf @method('DELETE')
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--color-text-secondary);">
            Belum ada tahun ajaran
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-pagination :paginator="$schoolYears" />
</div>
@endif

{{-- ══════════════════════════════════════════════ --}}
{{-- TAB: TINGKAT KELAS --}}
{{-- ══════════════════════════════════════════════ --}}
@if($activeTab === 'tingkat-kelas')
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Urutan</th>
          <th>Nama Tingkat</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($gradeLevels as $gl)
          <tr>
            <td>{{ $gl->order }}</td>
            <td><strong>{{ $gl->name }}</strong></td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                @php $glPayload = $gl->only(['name', 'order']); @endphp
                <button class="btn btn-sm btn-icon btn-outline" title="Edit"
                        onclick='openEditGradeLevelModal(@json($glPayload), "{{ hid($gl) }}")'>
                  <i class="ti ti-edit" style="font-size:13px;"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-danger" title="Hapus"
                        onclick="openDeleteModal('formDeleteGL{{ $gl->id }}')">
                  <i class="ti ti-trash" style="font-size:13px;"></i>
                </button>
                <form id="formDeleteGL{{ $gl->id }}" method="POST"
                      action="{{ route('master.grade-levels.destroy', hid($gl)) }}" style="display:none;">
                  @csrf @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="3" style="text-align:center;padding:30px;color:var(--color-text-secondary);">
            Belum ada tingkat kelas
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-pagination :paginator="$gradeLevels" />
</div>
@endif

{{-- ══════════════════════════════════════════════ --}}
{{-- TAB: MATA PELAJARAN --}}
{{-- ══════════════════════════════════════════════ --}}
@if($activeTab === 'mata-pelajaran')
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Nama Mapel</th>
          <th>Kode</th>
          <th>Tingkat Kelas</th>
          <th>Jurusan</th>
          <th>Status</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subjects as $subject)
          <tr>
            <td><strong>{{ $subject->name }}</strong></td>
            <td>{{ $subject->code ?? '-' }}</td>
            <td>{{ $subject->gradeLevel->name }}</td>
            <td>{{ $subject->major?->name ?? 'Umum' }}</td>
            <td>
              @if($subject->is_active)
                <span class="badge badge-green">Aktif</span>
              @else
                <span class="badge badge-red">Nonaktif</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                @php $subjectPayload = $subject->only(['grade_level_id', 'name', 'code', 'is_active']); @endphp
                <button class="btn btn-sm btn-icon btn-outline" title="Edit"
                        onclick='openEditSubjectModal(@json($subjectPayload), "{{ hid($subject) }}")'>
                  <i class="ti ti-edit" style="font-size:13px;"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-danger" title="Hapus"
                        onclick="openDeleteModal('formDeleteSub{{ $subject->id }}')">
                  <i class="ti ti-trash" style="font-size:13px;"></i>
                </button>
                <form id="formDeleteSub{{ $subject->id }}" method="POST"
                      action="{{ route('master.subjects.destroy', hid($subject)) }}" style="display:none;">
                  @csrf @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--color-text-secondary);">
            Belum ada mata pelajaran
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-pagination :paginator="$subjects" />
</div>
@endif

{{-- ══════════════════════════════════════════════ --}}
{{-- TAB: KKM --}}
{{-- ══════════════════════════════════════════════ --}}
@if($activeTab === 'kkm')
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Mata Pelajaran</th>
          <th>Tingkat</th>
          <th>Tahun Ajaran</th>
          <th>Nilai KKM</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($kkms as $kkm)
          <tr>
            <td><strong>{{ $kkm->subject->name }}</strong></td>
            <td>{{ $kkm->subject->gradeLevel->name }}</td>
            <td>{{ $kkm->schoolYear->name }}</td>
            <td>
              <span class="badge badge-green">{{ $kkm->kkm_score }}</span>
            </td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <button class="btn btn-sm btn-icon btn-danger" title="Hapus"
                        onclick="openDeleteModal('formDeleteKKM{{ $kkm->id }}')">
                  <i class="ti ti-trash" style="font-size:13px;"></i>
                </button>
                <form id="formDeleteKKM{{ $kkm->id }}" method="POST"
                      action="{{ route('master.kkm.destroy', hid($kkm)) }}" style="display:none;">
                  @csrf @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--color-text-secondary);">
            Belum ada data KKM
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <x-pagination :paginator="$kkms" />
</div>
@endif

{{-- ══════════════════════════════════════════════ --}}
{{-- MODAL KONFIRMASI HAPUS (generic) --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalDeleteConfirm">
  <div class="modal-box" style="max-width:360px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);">
        <i class="ti ti-alert-triangle"></i> Konfirmasi Hapus
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteConfirm')">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Apakah kamu yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteConfirm')">Batal</button>
      <button type="button" class="btn btn-danger" id="btnConfirmDelete">
        <i class="ti ti-trash"></i> Ya, Hapus
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH TAHUN AJARAN --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddSchoolYear">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-calendar"></i> Tambah Tahun Ajaran</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddSchoolYear')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="{{ route('master.school-years.store') }}">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Kurikulum</label>
          <select name="curriculum_id" class="form-control" required>
            <option value="">Pilih kurikulum</option>
            @foreach($curriculums as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Tahun Ajaran</label>
            <input type="text" name="year" class="form-control" placeholder="2024/2025" required>
            <div class="form-hint">Format: 2024/2025</div>
          </div>
          <div class="form-group">
            <label class="form-label required">Semester</label>
            <select name="semester" class="form-control" required>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
            </select>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label required">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddSchoolYear')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT TAHUN AJARAN --}}
<div class="modal-backdrop" id="modalEditSchoolYear">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-edit"></i> Edit Tahun Ajaran</div>
      <button type="button" class="modal-close" onclick="closeModal('modalEditSchoolYear')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" id="formEditSY">
      @csrf @method('PUT')
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Kurikulum</label>
          <select name="curriculum_id" id="editSYCurriculum" class="form-control" required>
            @foreach($curriculums as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Tahun Ajaran</label>
            <input type="text" name="year" id="editSYYear" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label required">Semester</label>
            <select name="semester" id="editSYSemester" class="form-control" required>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
            </select>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Tanggal Mulai</label>
            <input type="date" name="start_date" id="editSYStartDate" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label required">Tanggal Selesai</label>
            <input type="date" name="end_date" id="editSYEndDate" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditSchoolYear')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH TINGKAT KELAS --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddGradeLevel">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-stairs"></i> Tambah Tingkat Kelas</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddGradeLevel')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="{{ route('master.grade-levels.store') }}">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Nama Tingkat</label>
          <input type="text" name="name" class="form-control"
                 placeholder="Contoh: Kelas 1 / Kelompok A" required>
        </div>
        <div class="form-group">
          <label class="form-label required">Urutan Tampil</label>
          <input type="number" name="order" class="form-control"
                 placeholder="1" min="0" required>
          <div class="form-hint">Urutan kecil tampil lebih dulu (Kelas 1 = urutan 1)</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddGradeLevel')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT TINGKAT KELAS --}}
<div class="modal-backdrop" id="modalEditGradeLevel">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-edit"></i> Edit Tingkat Kelas</div>
      <button type="button" class="modal-close" onclick="closeModal('modalEditGradeLevel')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" id="formEditGL">
      @csrf @method('PUT')
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Nama Tingkat</label>
          <input type="text" name="name" id="editGLName" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label required">Urutan Tampil</label>
          <input type="number" name="order" id="editGLOrder" class="form-control" min="0" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditGradeLevel')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH MATA PELAJARAN --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddSubject">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-book"></i> Tambah Mata Pelajaran</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddSubject')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="{{ route('master.subjects.store') }}">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Tingkat Kelas</label>
          <select name="grade_level_id" class="form-control" required>
            <option value="">Pilih tingkat kelas</option>
            @foreach($gradeLevels as $gl)
              <option value="{{ $gl->id }}">{{ $gl->name }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Nama Mata Pelajaran</label>
            <input type="text" name="name" class="form-control"
                   placeholder="Contoh: Matematika" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" placeholder="MTK">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddSubject')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT MATA PELAJARAN --}}
<div class="modal-backdrop" id="modalEditSubject">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-edit"></i> Edit Mata Pelajaran</div>
      <button type="button" class="modal-close" onclick="closeModal('modalEditSubject')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" id="formEditSubject">
      @csrf @method('PUT')
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Tingkat Kelas</label>
          <select name="grade_level_id" id="editSubjectGL" class="form-control" required>
            @foreach($gradeLevels as $gl)
              <option value="{{ $gl->id }}">{{ $gl->name }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Nama Mata Pelajaran</label>
            <input type="text" name="name" id="editSubjectName" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kode</label>
            <input type="text" name="code" id="editSubjectCode" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="checkbox-item">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="editSubjectActive" value="1">
            Mata pelajaran aktif
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditSubject')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH KKM --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddKkm">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-target"></i> Tambah / Update KKM</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddKkm')"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="{{ route('master.kkm.store') }}">
      @csrf
      <div class="modal-body">
        <div class="info-box" style="margin-bottom:14px;">
          <i class="ti ti-info-circle"></i>
          Jika KKM untuk mata pelajaran & tahun ajaran yang sama sudah ada, nilainya akan diperbarui otomatis.
        </div>
        <div class="form-group">
          <label class="form-label required">Mata Pelajaran</label>
          <select name="subject_id" class="form-control" required>
            <option value="">Pilih mata pelajaran</option>
            @foreach($subjects as $sub)
              <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->gradeLevel->name }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Tahun Ajaran</label>
          <select name="school_year_id" class="form-control" required>
            <option value="">Pilih tahun ajaran</option>
            @foreach($schoolYears as $sy)
              <option value="{{ $sy->id }}" {{ $sy->is_active ? 'selected' : '' }}>
                {{ $sy->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Nilai KKM</label>
          <input type="number" name="kkm_score" class="form-control"
                 placeholder="75" min="0" max="100" step="0.5" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddKkm')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// Generic delete confirm
let pendingDeleteFormId = null;
function openDeleteModal(formId) {
  pendingDeleteFormId = formId;
  openModal('modalDeleteConfirm');
}
document.getElementById('btnConfirmDelete').addEventListener('click', () => {
  if (pendingDeleteFormId) {
    document.getElementById(pendingDeleteFormId).submit();
  }
});

// Edit Tahun Ajaran
function openEditSchoolYearModal(sy, hash) {
  document.getElementById('editSYCurriculum').value = sy.curriculum_id;
  document.getElementById('editSYYear').value = sy.year;
  document.getElementById('editSYSemester').value = sy.semester;
  document.getElementById('editSYStartDate').value = sy.start_date;
  document.getElementById('editSYEndDate').value = sy.end_date;
  document.getElementById('formEditSY').action = `/master-data/school-years/${hash}`;
  openModal('modalEditSchoolYear');
}

// Edit Tingkat Kelas
function openEditGradeLevelModal(gl, hash) {
  document.getElementById('editGLName').value = gl.name;
  document.getElementById('editGLOrder').value = gl.order;
  document.getElementById('formEditGL').action = `/master-data/grade-levels/${hash}`;
  openModal('modalEditGradeLevel');
}

// Edit Mata Pelajaran
function openEditSubjectModal(subject, hash) {
  document.getElementById('editSubjectGL').value = subject.grade_level_id;
  document.getElementById('editSubjectName').value = subject.name;
  document.getElementById('editSubjectCode').value = subject.code || '';
  document.getElementById('editSubjectActive').checked = subject.is_active;
  document.getElementById('formEditSubject').action = `/master-data/subjects/${hash}`;
  openModal('modalEditSubject');
}

// Close backdrop
document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});z
</script>
@endpush
