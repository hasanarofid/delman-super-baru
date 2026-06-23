<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait StakeholderAccess
{
    /**
     * Apply stakeholder multi-access filters to a query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $kabupatenColumn The column name for kabupaten_id (e.g., 'kabupaten_id' or 'users.kabupaten_id')
     * @param string|null $jenjangColumn The column name for jenjang (e.g., 'nama_sekolah'). If null, no jenjang filter is applied.
     * @param string|null $pengawasRelation The relation name to filter jenjang through pengawas's binaan (e.g., 'pengawasnama.sekolahbinaan.sekolah')
     * @param string|null $jenjangRelation The relation name to filter jenjang directly (e.g., 'sekolah')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyStakeholderFilter($query, $kabupatenColumn = 'kabupaten_id', $jenjangColumn = 'nama_sekolah', $pengawasRelation = null, $jenjangRelation = null)
    {
        $user = Auth::user();

        if (!$user) return $query;

        if ($user->role == 'Admin') {
            if ($user->kabupaten_id) {
                if (strpos($kabupatenColumn, '.') !== false) {
                    $parts = explode('.', $kabupatenColumn);
                    $kabRelation = implode('.', array_slice($parts, 0, -1));
                    $kabCol = end($parts);
                    $query->whereHas($kabRelation, function($q) use ($kabCol, $user) {
                        $q->where($kabCol, $user->kabupaten_id);
                    });
                } else {
                    $query->where($kabupatenColumn, $user->kabupaten_id);
                }
            }
            return $query;
        }

        if ($user->role == 'Stakeholder') {
            $akses_kabupaten = json_decode($user->akses_kabupaten, true) ?? [];
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];

            // 1. Filter by Kabupaten
            if (!in_array('All', $akses_kabupaten) && !empty($akses_kabupaten)) {
                if (strpos($kabupatenColumn, '.') !== false) {
                    $parts = explode('.', $kabupatenColumn);
                    $kabRelation = implode('.', array_slice($parts, 0, -1));
                    $kabCol = end($parts);
                    $query->whereHas($kabRelation, function($q) use ($kabCol, $akses_kabupaten) {
                        $q->whereIn($kabCol, $akses_kabupaten);
                    });
                } else {
                    $query->whereIn($kabupatenColumn, $akses_kabupaten);
                }
            } elseif (empty($akses_kabupaten)) {
                // If empty, force no results
                $query->whereRaw('1 = 0');
            }

            // 2. Filter by Jenjang
            if (!in_array('All', $akses_jenjang) && !empty($akses_jenjang)) {
                if ($pengawasRelation) {
                    // Filter by joining pengawas to sekolah_binaan
                    // If pengawasRelation is "users" (i.e. we are querying Users table)
                    if ($pengawasRelation === 'self') {
                        $query->whereExists(function($q) use ($akses_jenjang) {
                            $q->select(\Illuminate\Support\Facades\DB::raw(1))
                              ->from('sekolahbinaan_t')
                              ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                              ->whereRaw('sekolahbinaan_t.id_pengawas = users.id')
                              ->where(function($q2) use ($akses_jenjang) {
                                  foreach ($akses_jenjang as $jenjang) {
                                      $q2->orWhere('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang . '%');
                                  }
                              });
                        });
                    } else {
                        // $pengawasRelation is the relation name from the current model to the pengawas (User)
                        $query->whereHas($pengawasRelation, function($queryPengawas) use ($akses_jenjang) {
                            $queryPengawas->whereExists(function($q) use ($akses_jenjang) {
                                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                                  ->from('sekolahbinaan_t')
                                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                                  ->whereRaw('sekolahbinaan_t.id_pengawas = users.id')
                                  ->where(function($q2) use ($akses_jenjang) {
                                      foreach ($akses_jenjang as $jenjang) {
                                          $q2->orWhere('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang . '%');
                                      }
                                  });
                            });
                        });
                    }
                } elseif ($jenjangRelation) {
                    // Filter using whereHas for jenjang column
                    $query->whereHas($jenjangRelation, function($q) use ($akses_jenjang, $jenjangColumn) {
                        $q->where(function($q2) use ($akses_jenjang, $jenjangColumn) {
                            foreach ($akses_jenjang as $jenjang) {
                                $q2->orWhere($jenjangColumn, 'LIKE', '%' . $jenjang . '%');
                            }
                        });
                    });
                } elseif ($jenjangColumn) {
                    // Direct filter on nama_sekolah
                    $query->where(function($q) use ($akses_jenjang, $jenjangColumn) {
                        foreach ($akses_jenjang as $jenjang) {
                            $q->orWhere($jenjangColumn, 'LIKE', '%' . $jenjang . '%');
                        }
                    });
                }
            } elseif (empty($akses_jenjang)) {
                 $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}
