<?php
namespace App\Modules\Catastrocbba\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Modules\Catastrocbba\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Libraries\PhpMailer\MyPHPMailer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Modules\Catastrocbba\Models\Migracion;


class MigracionController extends Controller
{

	public function showByIdPredio($idPredio)
	{
		$listaPredios = DB::table('migracion as m')
            ->join('avaluo as a', 'a.idAvaluo', '=', 'm.idAvaluo')
            ->join('predio as p', 'p.idAvaluo', '=', 'a.idAvaluo')
            ->join('users as u','u.id','=','m.idUsuario')
            ->select('m.*','u.nombres','u.apellidos','u.login')
            ->where([
                ['p.idPredio', '=', $idPredio],
                ])
            ->get();
        return $listaPredios;
	}

}