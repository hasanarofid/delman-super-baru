      <!-- Edit User Modal -->
      <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-user">
          <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="text-center mb-4">
                <h3 class="mb-2">Tambah data Rencana Kerja</h3>
                <p class="text-muted">Inputkan data Rencana Kerja anda</p>
              </div>
              <form id="add_laporan" class="row g-3"  action="{{ route('pengawas.perencanaan.save-perencanaan') }}" method="POST" >
                @csrf
                <div class="col-12 col-md-6">
                    <label class="form-label" for="basic-default-name">Bulan</label>
                    <select class="form-control" id="bulan" name="bulan">
                      @foreach($months as $month)
                          <option value="{{ $month['name'] }}">
                              {{ $month['name'] }}
                          </option>
                      @endforeach
                    </select>                
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="basic-default-name">Tahun</label>
                   <input type="text" class="form-control"  id="tahun_ajaran" value="{{ date('Y') }}">  
              </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="basic-default-name">Nama Program Kerja</label>
                    <input placeholder="Nama Program Kerja" type="text" class="form-control"  name="nama_program_kerja" id="nama_program_kerja" required>

                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="basic-default-name">Kategori Program </label>
                    <select
                          id="kategoriprogram_id"
                          name="kategoriprogram_id"
                          class="select2 form-select"
                          required
                          >
                          <option value="">Select</option>
                          @foreach ($kategory as $item)
                              <option value="{{ $item->id }}">{{ $item->nama }}</option>
                          @endforeach
                        </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="basic-default-name">Jenis Program </label>
                  <select
                        id="jenisprogram_id"
                        name="jenisprogram_id"
                        class="select2 form-select"
                        required
                        >
                        <option value="">Select</option>
                        @foreach ($jenisProgram as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                      </select>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label" for="basic-default-name">Aspek Raport Pendidikan </label>
                <select
                      id="aspekprogram_id"
                      name="aspekprogram_id"
                      class="select2 form-select"
                      required
                      >
                      <option value="">Select</option>
                      @foreach ($aspekProgram as $item)
                          <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                    </select>
            </div>
                <div class="col-12 col-md-12">
                    <label class="form-label" for="basic-default-name">Sekolah Sasaran</label>
                    <select
                          id="sekolah_id"
                          name="sekolah_id[]"
                          class="select2 form-select "
                          multiple
                          >
                          <option value="">Select</option>
                          @foreach ($binaan as $item)
                          <option value="{{ $item->id_sekolah }}">{{ $item->sekolah->nama_sekolah }}</option>
                      @endforeach
                        </select>
                </div>
                

                  <div class="col-12">
                    <label class="form-label" for="deskripsi_permasalahan">Deskripsikan alasan membuat program kerja </label>
                    <textarea id="deskripsi_permasalahan" name="deskripsi_permasalahan" class="form-control"></textarea>
                </div>

                
                

                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                  <button
                    type="reset"
                    class="btn btn-label-secondary"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!--/ Edit User Modal -->

        <!-- Edit User Modal -->
        <div class="modal fade" id="editPerencanaan" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-simple modal-edit-user">
            <div class="modal-content p-3 p-md-5">
              <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                  <h3 class="mb-2">Edit data perencanaan</h3>
                  <p class="text-muted">Inputkan data perencanaan anda</p>
                </div>
                <form id="add_laporan" class="row g-3"  action="{{ route('pengawas.perencanaan.update') }}" method="POST" >
                  <input type="hidden" id="id" name="id">
                  @csrf
                  <div class="col-12 col-md-6">
                      <label class="form-label" for="basic-default-name">Bulan</label>
                      <select class="form-control" id="bulan_edit" name="bulan">
                        @foreach($months as $month)
                            <option value="{{ $month['name'] }}">
                                {{ $month['name'] }}
                            </option>
                        @endforeach
                      </select> 
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label" for="basic-default-name">Tahun</label>
                     <input type="text" class="form-control" disabled id="tahun_ajaran_edit" value="">
                </div>
                  <div class="col-12 col-md-6">
                      <label class="form-label" for="basic-default-name">Nama Program Kerja</label>
                      <input placeholder="Nama Program Kerja" type="text" class="form-control"  name="nama_program_kerja" id="nama_program_kerja_edit" required>
  
                  </div>
                  <div class="col-12 col-md-6">
                      <label class="form-label" for="basic-default-name">Kategori Program </label>
                      <select
                            id="kategoriprogram_id_edit"
                            name="kategoriprogram_id"
                            class="select2 form-select"
                            required
                            >
                            <option value="">Select</option>
                            @foreach ($kategory as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                          </select>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label" for="basic-default-name">Jenis Program </label>
                    <select
                          id="jenisprogram_id_edit"
                          name="jenisprogram_id"
                          class="select2 form-select"
                          required
                          >
                          <option value="">Select</option>
                          @foreach ($jenisProgram as $item)
                              <option value="{{ $item->id }}">{{ $item->nama }}</option>
                          @endforeach
                        </select>
                </div>
  
                <div class="col-12 col-md-6">
                  <label class="form-label" for="basic-default-name">Aspek Raport Pendidikan </label>
                  <select
                        id="aspekprogram_id_edit"
                        name="aspekprogram_id"
                        class="select2 form-select"
                        required
                        >
                        <option value="">Select</option>
                        @foreach ($aspekProgram as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                      </select>
              </div>

              <div class="col-12 col-md-6">
                  <label class="form-label" for="id_umpanbalik_category_edit">Kategori Umpan Balik</label>
                  <select
                        id="id_umpanbalik_category_edit"
                        name="id_umpanbalik_category"
                        class="select2 form-select"
                        required
                        >
                        <option value="">Pilih Kategori</option>
                        <option value="0">Umpan Balik Default (Statis)</option>
                        @foreach ($umpanbalikCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                      </select>
              </div>
                  <div class="col-12 col-md-12">
                      <label class="form-label" for="basic-default-name">Sekolah Sasaran</label>
                      <select
                            id="sekolah_id_edit"
                            name="sekolah_id[]"
                            class="select2 form-select "
                            multiple
                            >
                            <option value="">Select</option>
                            @foreach ($binaan as $item)
                            <option value="{{ $item->id_sekolah }}">{{ $item->sekolah->nama_sekolah }}</option>
                        @endforeach
                          </select>
                  </div>
                
  
  
                    <div class="col-12">
                      <label class="form-label" for="deskripsi_permasalahan">Deskripsikan alasan membuat program kerja </label>
                      <textarea id="deskripsi_permasalahan_edit" name="deskripsi_permasalahan" class="form-control"></textarea>
                  </div>
  

                  
  
                  <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button
                      type="reset"
                      class="btn btn-label-secondary"
                      data-bs-dismiss="modal"
                      aria-label="Close">
                      Cancel
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!--/ Edit User Modal -->

