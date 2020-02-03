<?php

namespace Pixxo\RestFullApi;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use function foo\func;

trait ApiResponser{
    /**
     * @param $data
     * @param string|null $message
     * @return ResponseFactory|Response
     */
    protected function successResponse($data = null, string $message = null){
        return response([
            'data' => $data,
            'message' => $message,
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    /**
     * @param string|null $message
     * @param int $responseCode
     * @return ResponseFactory|Response
     */
    protected function errorResponse(string $message = null, int $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR){
        return response([
            'data' => null,
            'message' => $message,
            'status' => 'error',
        ], $responseCode);
    }

    /**
     * @param Collection $collection
     * @return ResponseFactory|Response
     */
    protected function collectionResponse(Collection $collection){
        if($collection->isEmpty()){
            return $this->successResponse();
        }

        // sort collection
        $collection = $this->filterData($collection);
        $collection = $this->sortData($collection);

        if(request()->has("paginate")){
            if(request()->paginate == "1" || strtolower(request()->paginate) == "true"){
                $collection = $this->paginateData($collection);
            }
        }

        return $this->successResponse($collection);
    }

    /**
     * @param Collection $collection
     * @return Collection|mixed
     */
    private function sortData(Collection $collection) : Collection{
        if(request()->has("sort_by")){
            $attribute = request()->sort_by;
            $collection = $collection->sortBy($attribute);
        }
        return $collection;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    private function filterData(Collection $collection) : Collection{
        foreach (request()->query() as $query => $value){
            if(isset($collection[0]->$query)){
                $collection = $collection->where($query, $value);
            }
        }
        return $collection;
    }

    private function paginateData(Collection $collection){
        $rules = [
            "per_page" => "integer|min:2|max:50",
        ];

        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;
        if(request()->has("per_page")){
            $perPage = (int) request()->per_page;
        }

        $result = $collection->slice(($page-1)*$perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $page, [
            "path" => LengthAwarePaginator::resolveCurrentPage()
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }
}
