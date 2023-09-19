<?php

namespace App\Modules\Catastrocbba\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Modules\Catastrocbba\Models\DatosPropietario;
use Illuminate\Support\Facades\DB;

class DatosPropietarioController extends Controller
{
    public function showAll() {
    	try{

            $response = [
                'data' => []
            ];
            $statusCode = 200;
            $datospropietarios = DatosPropietario::get();
            $count = 0;
            foreach($datospropietarios as $datospropietario){
            	$response["data"][$count]["type"] ="datospropietario"; 
            	$response["data"][$count]["id"]=$datospropietario->idDatoJuridico;
            	$response["data"][$count]["attributes"]["idpredio"]=$datospropietario->idPredio;
            	$response["data"][$count]["attributes"]["apellidouno"] = $datospropietario->apellidoUno;
            	$response["data"][$count]["attributes"]["apellidodos"] = $datospropietario->apellidoDos; 
            	$response["data"][$count]["attributes"]["nombres"] = $datospropietario->nombres;
            	$response["data"][$count]["attributes"]["numerodocumento"] = $datospropietario->numeroDocumento;
            	$response["data"][$count]["attributes"]["idemitidoen"]=$datospropietario->idEmitidoEn;
            	$response["data"][$count]["attributes"]["numeronit"] = $datospropietario->numeroNIT;
            	$response["data"][$count]["attributes"]["porcentaje"] = $datospropietario->porcentaje; 
            	$response["data"][$count]["attributes"]["estado"] = $datospropietario->estado; 
            	$response["data"][$count]["attributes"]["notario"] = $datospropietario->notario;
            	$count = $count + 1;               
            }    
        }catch (\Exception $e){
            $statusCode = 404;
        }finally{
            return Response::json($response, $statusCode);
        }
	}
	public static function getListDatosPropietarioByIdUsuario($idUsuario){
	    $listaDatosPropietario = DB::table('datos_propietario as dp')
	    ->join('predio as p', 'p.idPredio', '=', 'dp.idPredio')
	    ->join('avaluo as a', 'a.idAvaluo', '=', 'p.idAvaluo')
	    ->select('dp.*')
	    ->where('a.idUsuario', '=', $idUsuario)
	    ->get();
	    return $listaDatosPropietario;
	}
	
	public function store(Request $request){
	    try{
	        $input = $request->all();
	        $id = DB::table('datos_propietario')->insertGetId($input);
	    }catch(\Exception $e){
	        return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString(),"last_insert_id"=>null), 200);
	    }
	    return Response::json(array('success' => true, 'mensaje' => "OK","last_insert_id"=>$id), 200);
	}
	public function update(Request $request){
	
	    try{
	        $input = $request->all();
	        DatosPropietario::where('idDatosPropietario', $input["idDatosPropietario"])->update($input);
	
	    }catch(\Exception $e){
	        return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString()), 200);
	    }
	    return Response::json(array('success' => true, 'mensaje' => "OK"), 200);
	}
	
	public static function getListDatosPropietarioByIdPredio($idPredio){
	    $listaDatosPropietario = DatosPropietario::where('idPredio','=',  $idPredio)->get();
	    return $listaDatosPropietario;
	}

	public function getPropietarioByPredio($idPredio){
        	$propietario = DatosPropietario::where('idPredio', '=', $idPredio)->first();
        	return $propietario; 
    	}
}
