<?php

namespace App\Modules\Catastrocbba\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Modules\Catastrocbba\Models\ValoresBloque;
use Illuminate\Support\Facades\DB;

class ValoresBloqueController extends Controller
{
   public function showAll() {
    	try{

            $response = [
                'data' => []
            ];
            $statusCode = 200;
            $valoresbloques = ValoresBloque::get();
            $count = 0;
            foreach($valoresbloques as $valoresbloque){
            	$response["data"][$count]["type"] ="valoresbloque"; 
            	$response["data"][$count]["id"]=$valoresbloque->idValorBloque;
            	$response["data"][$count]["attributes"]["idBloqueDato"]=$valoresbloque->idBloqueDato;
            	$response["data"][$count]["attributes"]["idCaracteristiscaBloque"] = $valoresbloque->idCaracteristiscaBloque;
            	$response["data"][$count]["attributes"]["orden"]=$valoresbloque->orden;
            	$response["data"][$count]["attributes"]["porcentaje"] =$valoresbloque->porcentaje; 
            	$response["data"][$count]["attributes"]["puntaje"] =$valoresbloque->puntaje;
            	$response["data"][$count]["attributes"]["estado
            	"] = $valoresbloque->estado; 
            	$count = $count + 1;               
            }    
        }catch (\Exception $e){
            $statusCode = 404;
        }finally{
            return Response::json($response, $statusCode);
        }
        //return Usuario::get();
    }

    public function show($id) {
        try{
            $valoresbloque = ValoresBloque::find($id);
            $statusCode = 200;
            /*$response = [ "usuario" => [
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'login' => $usuario->login
            ]];*/
            $response = array("data"=>array("type"=>"valoresbloque",
            								"id"=>$valoresbloque->idValorBloque,
            								"attributes"=>array(
            									"idBloqueDato" => $valoresbloque->idBloqueDato,
            									"idCaracteristiscaBloque" => $valoresbloque->idCaracteristiscaBloque,
	            								"orden"=>$valoresbloque->orden,
	            								"porcentaje" => $valoresbloque->porcentaje,
	                							"puntaje" => $valoresbloque->puntaje,
	                							"estado" => $valoresbloque->estado)));
        }catch(\Exception $e){
            $response = [
                "error" => "File doesn`t exists"
            ];
            $statusCode = 404;
        }finally{
            return Response::json($response, $statusCode);
        }        
    }

    public function store(Request $request) {
        //return 'VAlor;'.$request->input('type');
        try{
            $input = $request->all();
            $id = DB::table('valores_bloque')->insertGetId($input);
       }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString(),"last_insert_id"=>null), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK","last_insert_id"=>$id), 200);
    }
    public static function getListValorBloqueByIdUsuario($idUsuario){
        $listValorBloque = DB::table('valores_bloque as vb')
        ->join('bloques_dato as bd', 'bd.idBloqueDato', '=', 'vb.idBloqueDato')
        ->join('predio as p', 'p.idPredio', '=', 'bd.idPredio')
        ->join('avaluo as a', 'a.idAvaluo', '=', 'p.idAvaluo')
        ->select('vb.*')
        ->where('a.idUsuario', '=', $idUsuario)
        ->get();
        return $listValorBloque;
    }
	
	public function update(Request $request){
    
        try{
            $input = $request->all();
            ValoresBloque::where('idValorBloque', $input["idValorBloque"])->update($input);
    
        }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString()), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK"), 200);
    }
    
    public static function getListValorBloqueByIdBloqueDato($idBloqueDato){
        $listValorBloque = DB::table('valores_bloque as vb')
        ->join('bloques_dato as bd', 'bd.idBloqueDato', '=', 'vb.idBloqueDato')
        ->select('vb.*')
        ->where([['vb.idBloqueDato', '=', $idBloqueDato],['vb.estado','=','AC']])
        ->get();
        return $listValorBloque;
    }
    public static function getSumaPuntajeByIdBloqueDato($idBloqueDato){
        
        
        $listValorBloque=ValoresBloque::where([['idBloqueDato', '=', $idBloqueDato],['estado','=','AC']])->get();
        $suma=0;
        foreach ($listValorBloque as $valorBloque){
            $suma+=$valorBloque->puntaje;
        }
        return $suma;
    }
    public static function getListValorBloqueByIdPredio($idPredio){
        $listValorBloque = DB::table('valores_bloque as vb')
        ->join('bloques_dato as bd', 'bd.idBloqueDato', '=', 'vb.idBloqueDato')
        ->select('vb.*')
        ->where([['vb.estado','=','AC'],['bd.idPredio','=',$idPredio]])
        ->get();
        return $listValorBloque;
    }
}
