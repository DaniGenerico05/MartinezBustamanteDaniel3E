<?php

function getProviderInfo($num){
	$query = "SELECT num,fiscal_name,numTel,email from provider where num= ".$num;
	$db = connect();
    if(!$resultado = mysqli_query($db,$query)){
		exit(mysqli_error($db));
    }
	$infoProvider = null;
    
    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $infoProvider = array(
            'num' => $row['num'],
            'fiscalName' => $row['fiscal_name'],
            'numTel' => $row['numTel'],
            'email' => $row['email']
        );
    }
	return $infoProvider;	
}

function getRequestInfo($requestNum){
    
    $requestNum = intval($requestNum);
    
    $query = "
        SELECT 
            r.num AS requestNum,
            r.request_date,
            sr.name AS status_name,
            CONCAT(e.firstName, ' ', e.lastName) AS employee_name,
            p.fiscal_name AS provider_name,
            GROUP_CONCAT(
                CONCAT(
                    rm.material, ': ',
                    m.name,
                    ' (Quantity: ', rm.quantity,
                    ', Amount: $', rm.amount, ')'
                ) SEPARATOR '<br>'
            ) AS materials_detail
        FROM request r
        LEFT JOIN employee e ON r.employee = e.num
        LEFT JOIN provider p ON r.provider = p.num
        LEFT JOIN request_material rm ON r.num = rm.request
        LEFT JOIN raw_material m ON rm.material = m.code
        LEFT JOIN status_request sr ON r.status = sr.code
        WHERE r.num = $requestNum  
       
    ";

    $db = connect();
    if (!$resultado = mysqli_query($db, $query)) {
        exit(mysqli_error($db));
    }
    
    $infoRequest = null;

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $infoRequest = array(
            'requestNum' => $row['requestNum'],
            'requestDate' => $row['request_date'],
            'employee' => $row['employee_name'],  
            'provider' => $row['provider_name'],  
            'materials_detail' => $row['materials_detail'],
            'status' => $row['status_name']      
            
        );
    }

    return $infoRequest;
}


function getReceptionInfo($num) {
    $db = connect();
    $query = "
        SELECT 
            r.num AS reception_num,
            r.receptionDate,
            r.observations,
            r.missings,
            CONCAT(e.firstName, ' ', e.lastName) AS employee_name,
            r.request,
            r.status,
            s.name AS status_name,
            req.request_date AS request_date,
            CONCAT(p.fiscal_name) AS provider_name
        FROM reception r
        LEFT JOIN employee e ON r.employee = e.num
        LEFT JOIN request req ON r.request = req.num
        LEFT JOIN provider p ON req.provider = p.num
        LEFT JOIN status_reception s ON r.status = s.code
        WHERE r.num = ".$num; 

    if(!$resultado = mysqli_query($db, $query)) {
        exit(mysqli_error($db));  
    }

    $infoReception = null;
    
    if (mysqli_num_rows($resultado) > 0) {

        $row = mysqli_fetch_assoc($resultado);
        
  
        $infoReception = array(
            'reception_num' => $row['num'],
            'receptionDate' => $row['receptionDate'],
            'observations' => $row['observations'],
            'missings' => $row['missings'],
            'employee' => $row['employee'],
            'request' => $row['request'],
            'status' => $row['status'],
        );
    }
    

    return $infoReception;
}


function getEmployeeInfo($num) {
    $db = connect();
    $query = "SELECT
                e.num AS num,
                CONCAT(
                    IFNULL(e.firstName, ''), ' ',
                    IFNULL(e.lastName, ''), ' ',
                    IFNULL(e.surname, '')
                ) AS name,
                e.status AS status,
                e.numTel AS numTel,
                e.email AS email,
                c.name AS charge,
                a.name AS area
              FROM employee AS e
              INNER JOIN charge AS c ON e.charge = c.code
              INNER JOIN area AS a ON e.area = a.code
              WHERE e.num = ?";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $num);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado) {
        exit("Error en la consulta: " . mysqli_error($db));
    }

    $infoEmployee = null;
    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $infoEmployee = array(
            'num' => $row['num'],
            'name' => $row['name'],
            'status' => $row['status'],
            'numTel' => $row['numTel'],
            'email' => $row['email'],
            'charge' => $row['charge'],
            'area' => $row['area']
        );
    }


    mysqli_close($db);

    return $infoEmployee;
}

?>