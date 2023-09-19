<?php

namespace App\Modules\Catastrocbba\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Modules\Catastrocbba\Models\BloquesDato;
use Illuminate\Support\Facades\DB;
use App\Modules\Catastrocbba\Models\ValoresBloque;
use Illuminate\Database\Eloquent\Model;

class BloquesDatoController extends Controller
{
    public function showAll() {
    	try{

            $response = [
                'data' => []
            ];
            $statusCode = 200;
            $bloquesDatos = BloquesDato::get();
            $count = 0;
            foreach($bloquesDatos as $bloquesDato){
            	$response["data"][$count]["type"] ="bloquesDato"; 
            	$response["data"][$count]["id"]=$bloquesDato->idBloquesDato;
            	$response["data"][$count]["attributes"]["numerobloque"]=$bloquesDato->numerobloque;
            	$response["data"][$count]["attributes"]["superficiebloque"] = $bloquesDato->superficieBloque;
            	$response["data"][$count]["attributes"]["anioconstruccion"] = $bloquesDato->anioConstruccion; 
            	$response["data"][$count]["attributes"]["cantidadpisos"]=$bloquesDato->cantidadPisos;
            	$response["data"][$count]["attributes"]["idcoeficienteUso"] = $bloquesDato->idCoeficienteUso;
            	$response["data"][$count]["attributes"]["idcoeficienteDepreciacion"] = $bloquesDato->idCoeficienteDepreciacion; 
            	$response["data"][$count]["attributes"]["observaciones"]=$bloquesDato->observaciones;
            	$response["data"][$count]["attributes"]["tipobloque"] = $bloquesDato->tipoBloque;
            	$response["data"][$count]["attributes"]["estado"] = $bloquesDato->estado; 
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
            $bloquesDato = BloquesDato::find($id);
            $statusCode = 200;
            $response = array("data"=>array("type"=>"bloquesDato",
            								"id"=>$bloquesDato->idBloqueDato,
            								"attributes"=>array(
	            								"numerobloque"=>$bloquesDato->numerobloque,
	            								"superficiebloque" => $bloquesDato->superficieBloque,
	            								"anioconstruccion"=>$bloquesDato->anioConstruccion,
	            								"cantidadpisos" => $bloquesDato->cantidadPisos,
	            								"idCoeficienteuso"=>$bloquesDato->idCoeficienteUso,
	            								"idCoeficientedepreciacion" => $bloquesDato->idCoeficienteDepreciacion,
	            								"observaciones"=>$bloquesDato->observaciones,
	            								"tipobloque" => $bloquesDato->tipoBloque,
	                							"estado" => $bloquesDato->estado)));
        }catch(\Exception $e){
            $response = [
                "error" => "File doesn`t exists"
            ];
            $statusCode = 404;
        }finally{
            return Response::json($response, $statusCode);
        }        
    }
    public static function getListBloquesDatoByIdUsuario($idUsuario){
        $listaBloquesDato = DB::table('bloques_dato as bd')
        ->join('predio as p', 'p.idPredio', '=', 'bd.idPredio')
        ->join('avaluo as a', 'a.idAvaluo', '=', 'p.idAvaluo')
        ->select('bd.*')
        ->where('a.idUsuario', '=', $idUsuario)->where('bd.estado','=','AC')
        ->get();
        return $listaBloquesDato;
    }
    public function getListBloquesDatoByIdUsuarioTEST($idUsuario){
        $listaBloquesDato = DB::table('bloques_dato as bd')
        ->join('predio as p', 'p.idPredio', '=', 'bd.idPredio')
        ->join('avaluo as a', 'a.idAvaluo', '=', 'p.idAvaluo')
        ->select('bd.*')
        ->where('a.idUsuario', '=', $idUsuario)->where('bd.estado','=','AC')
        ->get();
        return $listaBloquesDato;
    }
    public static function getListBloquesDatoByIdPredio($idPredio){
        $listaBloquesDato = DB::table('bloques_dato as bd')
        ->join('predio as p', 'p.idPredio', '=', 'bd.idPredio')
        ->select('bd.*')
        ->where('p.idPredio', '=', $idPredio)->where('bd.estado','=','AC')
        ->get();
        return $listaBloquesDato;
    }
    public function store(Request $request){
        
        try{
            $input = $request->all();
            $id = DB::table('bloques_dato')->insertGetId($input);
       }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString(),"last_insert_id"=>null), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK","last_insert_id"=>$id), 200);
    }
    public function delete(Request $request){
        $input = $request->all();
        try{
            
            $input['estado']="EL";
            BloquesDato::where('idBloqueDato', $input["idBloqueDato"])->update($input);
    
        }catch(\Exception $e){
            return Response::json(array('success' => false, "ingreso"=>$input,'mensaje' => "Exception","stackTrace"=>$e->__toString()), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK"), 200);
    }
    public function storeBloqueDatoAndValoresBloque(Request $request){
        try{
            $bloquesDatoJson=$request->input("bloquesDato");
            $bloquesDatoJson["estado"]="AC";
            $listaValoresBloqueJson=$request->input("listaValoresBloque");
            $idBloqueDato = DB::table('bloques_dato')->insertGetId($bloquesDatoJson);
            
            $listaValoresBloque=array();
            if($idBloqueDato!=null){
            
                foreach ($listaValoresBloqueJson as $valorBloqueTmp){
                    $valorBloque=new ValoresBloque();
                    $valorBloque->idBloqueDato=$idBloqueDato;
                    $valorBloque->idCaracteristicaBloque=$valorBloqueTmp["idCaracteristicaBloque"];
                    $valorBloque->orden=$valorBloqueTmp["orden"];
                    if($valorBloqueTmp["porcentaje"]>100)
                        $valorBloque->porcentaje=100;
                    else $valorBloque->porcentaje=$valorBloqueTmp["porcentaje"];
                    $valorBloque->puntaje=$valorBloqueTmp["puntaje"];
                    $valorBloque->estado=$valorBloqueTmp["estado"];
                    
                    $valorBloque->save();
                    $listaValoresBloque[]=$valorBloque;
                }
            
            }
        }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString(),"idBloquesDato"=>null,"listaValoresBloque"=>null), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK","idBloquesDato"=>$idBloqueDato,"listaValoresBloque"=>$listaValoresBloque), 200);
        
    }
    public function updateBloqueDatoAndValoresBloque(Request $request){
        
        try{
            $bloquesDatoJson=$request->input("bloquesDato");
            $listaValoresBloqueJson=$request->input("listaValoresBloque");
            
            BloquesDato::where('idPredio', $bloquesDatoJson["idBloqueDato"])->update($bloquesDatoJson);
            ValoresBloque::where('idBloqueDato','=' ,$bloquesDatoJson["idBloqueDato"])->delete();
        
            $listaValoresBloque=array();
            if($bloquesDatoJson["idBloqueDato"]!=null){
        
                foreach ($listaValoresBloqueJson as $valorBloqueTmp){
                    $valorBloque=new ValoresBloque();
                    $valorBloque->idBloqueDato=$bloquesDatoJson["idBloqueDato"];
                    $valorBloque->idCaracteristicaBloque=$valorBloqueTmp["idCaracteristicaBloque"];
                    $valorBloque->orden=$valorBloqueTmp["orden"];
                    $valorBloque->porcentaje=$valorBloqueTmp["porcentaje"];
                    $valorBloque->puntaje=$valorBloqueTmp["puntaje"];
                    $valorBloque->estado=$valorBloqueTmp["estado"];
        
                    $valorBloque->save();
                    $listaValoresBloque[]=$valorBloque;
                }
        
            }
        }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString(),"listaValoresBloque"=>null), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK","listaValoresBloque"=>$listaValoresBloque), 200);
    }
    public function update(Request $request){
    
        try{
            $input = $request->all();
            BloquesDato::where('idBloqueDato', $input["idBloqueDato"])->update($input);
            
        }catch(\Exception $e){
            return Response::json(array('success' => false, 'mensaje' => "Exception","stackTrace"=>$e->__toString()), 200);
        }
        return Response::json(array('success' => true, 'mensaje' => "OK"), 200);
    }
    public function getBloques($idPredio){
        $listaBloquesDato = DB::table('bloques_dato as bd')
        ->join('coeficiente_depreciacion as cd', 'cd.idCoeficienteDepreciacion', '=', 'bd.idCoeficienteDepreciacion')
        ->join('coeficiente_uso as cu', 'cu.idCoeficienteUso', '=', 'bd.idCoeficienteUso')
        ->select('bd.*','cd.descripcion as coeficienteDepreciacion','cu.descripcion as coeficienteUso')
        ->where('bd.idPredio','=',$idPredio)->where('bd.estado','=','AC')->where('bd.tipoBloque','=','0')->orderBy('numerobloque','asc')->orderBy('gestion','asc')
        ->get();
        return $listaBloquesDato;
    } 
    public function getMejoras($idPredio){
        $listaBloquesDato = DB::table('bloques_dato as bd')
        ->join('coeficiente_depreciacion as cd', 'cd.idCoeficienteDepreciacion', '=', 'bd.idCoeficienteDepreciacion')
        ->join('tipo_mejora as tm', 'tm.idTipoMejora', '=', 'bd.idTipoMejora')
        ->select('bd.*','cd.descripcion as coeficienteDepreciacion','tm.descripcion as tipoMejora')
        ->where('bd.idPredio','=',$idPredio)->where('bd.estado','=','AC')->where('bd.tipoBloque','=','1')->orderBy('tm.descripcion','asc')->orderBy('gestion','asc')
        ->get();
        return $listaBloquesDato;
    }
}
