<?php
 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: text/json; charset=utf-8");


include "Connect/connect.php";

$postjson = json_decode(file_get_contents('php://input'),true);


if($postjson['crud'] == "listar-subcategorias"){

    $data = array();
    
    $query = mysqli_query($mysqli, "SELECT * FROM subcategoria as c ORDER BY c.id desc LIMIT $postjson[start], $postjson[limit]");

    while($row = mysqli_fetch_array($query)){
        $data[] = array(
            'id'            => $row['id'],
            'nome'          => $row['nome'],
            'foto'          => $row['foto'],
            'categoria_id'  => $row['categoria_id']
            
        );
    }

    if($query) $result = json_encode(array('success' => true,'result' =>$data));
    else $result = json_encode(array('success'=> false));
    echo $result;

}


elseif($postjson['crud'] == "add-item"){
   
    $data = array();

    $query   = mysqli_query($mysqli, "INSERT INTO pedido SET
               
               nome            = '$postjson[nome]',
               qtd             = '$postjson[qtd]',
               valor           = '$postjson[valor]',
               foto            = '$postjson[foto]',
               status          = '$postjson[status]'
               ");

    $idadd = mysqli_insert_id($mysqli);

    if($query) $result = json_encode(array('success' => true, 'idadd' => $idadd));
    else $result = json_encode(array('success'=> false));
    echo $result;

}

elseif($postjson['crud'] == "editar"){

    $data = array();

    $radom     = date('Y-m-d_H_i_s');

    $entry     = base64_decode($postjson['foto']);

    $img       = imagecreatefromstring($entry);

    $directory = "./imgs/img_user".$radom.".jpg";

    imagejpeg($img, $directory);

    imagedestroy($img);



    $query   = mysqli_query($mysqli, "UPDATE subcategoria SET
           
     
    nome           =  '$postjson[nome]',
    categoria_id   =  '$postjson[categoria_id]',
    foto           =  '$directory' WHERE id  = '$postjson[id]'");

    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false));
    echo $result;
}

elseif($postjson['crud'] == "editar2"){

    $data = array();

    $query   = mysqli_query($mysqli, "UPDATE subcategoria SET
           
     
           nome  =  '$postjson[nome]',
           categoria_id   =  '$postjson[categoria_id]',
           foto    =  '$postjson[foto]' WHERE id  = '$postjson[id]'");
           
    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false));
    echo $result;
}

elseif($postjson['crud'] == "deletar"){

    $query   = mysqli_query($mysqli, "DELETE FROM subcategoria WHERE id  = '$postjson[id]'");
  

    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false, 'msg'=>'error, Por favor, tente novamente... '));
    echo $result;
}

?>

