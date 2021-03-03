<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Models\Company;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $company = Company::find($request->route('company_id'));

        if ($company) {

            if ($company->user_id != Auth::id()) {

                throw new CustomException('Empresa não autorizada.', 403);

            }

            if ($company->status == Company::STATUS_INACTIVE) {

                throw new CustomException('Empresa em análise.', 403);

            }
            
            if ($company->status == Company::STATUS_SUSPENDED) {

                throw new CustomException('Empresa suspensa.', 403);

            }

            return $next($request);

        }

        else {

            throw new CustomException('Empresa não encontrada.', 404);

        }
    }
}
