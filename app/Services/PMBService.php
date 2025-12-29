<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PMBService
{
    /**
     * Get API Key from config
     */
    protected static function getApiKey(): string
    {
        return config('api.key');
    }

    /**
     * Get Base URL from config
     */
    protected static function getBaseUrl(): string
    {
        return rtrim(config('api.url'), '/') . '/';
    }

    /**
     * Get all siswa
     * @param int|null $offset offset, default null
     * @param int|null $limit limit, default null
     * @param string|null $search search siswa, default null
     * @param string|null $order order siswa
     * @param string|null $dir dir siswa, default null asc or desc
     * @param array|null $where where siswa
     * @return object|null data siswa
     */
    public static function all($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        $post = [
            'offset' => $offset,
            'limit' => $limit,
            'search' => $search,
            'order' => $order,
            'dir' => $dir,
            'where' => $where != null ? json_encode($where) : null,
        ];

        $response = Http::withHeaders([
            'apikey' => self::getApiKey(),
        ])->post(self::getBaseUrl() . 'siswa/all', $post);

        return $response->object();
    }

    /**
     * Find siswa by id
     * @param int $id
     * @return object|null data siswa
     */
    public static function find($id)
    {
        $post = [
            'id' => $id,
        ];

        $response = Http::withHeaders([
            'apikey' => self::getApiKey(),
        ])->post(self::getBaseUrl() . 'siswa/find', $post);

        return $response->object();
    }

    /**
     * Get count siswa
     * @param int|null $offset offset, default null
     * @param int|null $limit limit, default null
     * @param string|null $search search siswa, default null
     * @param string|null $order order siswa
     * @param string|null $dir dir siswa, default null asc or desc
     * @param array|null $where where siswa
     * @return object|null data count
     */
    public static function count($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        $post = [
            'offset' => $offset,
            'limit' => $limit,
            'search' => $search,
            'order' => $order,
            'dir' => $dir,
            'where' => $where != null ? json_encode($where) : null,
        ];

        $response = Http::withHeaders([
            'apikey' => self::getApiKey(),
        ])->post(self::getBaseUrl() . 'siswa/count', $post);

        return $response->object();
    }

    /**
     * Get foto siswa by NIK
     * @param string $nik NIK siswa
     * @return object|null data foto
     */
    public static function foto($nik, $strata)
    {
        $post = [
            'nik' => $nik,
            'strata' => $strata
        ];

        $response = Http::withHeaders([
            'apikey' => self::getApiKey(),
        ])->post(self::getBaseUrl() . 'siswa/foto', $post);

        return $response->object();
    }
}
