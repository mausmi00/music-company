<!doctype html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
    <?php
    require __DIR__ . "/sql_functions.php";

    if (!connectToDB()) {
        echo "Could not connect to DB";
    } else {

        $labelQueryResult = executePlainSQL("
                    select * from contract
                    natural join label
                    natural join band
                    natural join agent
                    ");
        $contracts = [];

        while (($row = oci_fetch_assoc($labelQueryResult))) {
            $agent = array(
                "AGENTID" => trim($row['AGENTID']),
                "AGENTNAME" => trim($row['AGENTNAME']),
                "LABELNAME" => trim($row['LABELNAME'])
            );
            $label = array(
                "LABELNAME" => trim($row['LABELNAME']),
                "STREETNUMBER" => trim($row['BANDNAME']),
                "COUNTRY" => trim($row["SIGNINGBONUS"]),
                "POSTALCODE" => trim($row["ROYALTY"])
            );
            if (empty($contracts[$row['CONTRACTID']])) {
                $contracts[$row['CONTRACTID']] = array(
                    "CONTRACTID" => trim($row['CONTRACTID']),
                    "SIGNINGBONUS" => trim($row["SIGNINGBONUS"]),
                    "ROYALTY" => trim($row["ROYALTY"]),
                    "TIMEBASEDCONTRACTDURATION" => trim($row["TIMEBASEDCONTRACTDURATION"]),
                    "CONTENTBASEDNUMBEROFALBUMS" => trim($row["CONTENTBASEDNUMBEROFALBUMS"]),
                    "BANDNAME" => trim($row['BANDNAME']),
                    "LABELNAME" => trim($row['LABELNAME']),
                    "AGENTS" => array($agent)
                );
            } else {
                array_push(
                    $labels[$row['CONTRACTID']]['AGENTS'],
                    array($agent)
                );
            }
        }
    }

    ?>
    <header>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand text-success" href="#">Record Company</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="labels.php">Labels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php">Groups</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contracts.php">Contracts</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <ul class="list-group list-group-flush">

            <li class="list-group-item justify-content-evenly align-items-start">
                <div class="container">

                    <div class="row">
                        <div class="col">

                            <h1> Contracts </h1>
                        </div>
                        <div class="col">

                            <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Add Contract
                            </button>

                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add New Contract</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" id="addContractRequest" name="addContractRequest">

                                                <?php
                                                $contractFields = array(
                                                    "Contract ID:",
                                                    "Signing Bonus (USD):",
                                                    "Royalty:",
                                                    "Contract Duration:",
                                                    "Album Quota:",
                                                );

                                                $contractResult = array(
                                                    "CONTRACTID" => "",
                                                    "SIGNINGBONUS" => "",
                                                    "ROYALTY" => "",
                                                    "TIMEBASEDCONTRACTDURATION" => "",
                                                    "CONTENTBASEDNUMBEROFALBUMS" => "",
                                                    "AGENTS" => array(),
                                                    "BAND" => "",
                                                );
                                                ?>

                                                <?php foreach ($contractFields as $key => $contractField): ?>

                                                <div class="form-group row pb-1">
                                                    <label for="<?php echo $contractField ?>"
                                                        class="col-sm-5 col-form-label">
                                                        <?php echo $contractField ?>
                                                    </label>

                                                    <div class="col-sm">
                                                        <input type="text" class="form-control"
                                                            name="contractResult[<?php echo $key ?>]"
                                                            id="<?php echo $contractField ?>" <?php if ($key < 3) { echo
                                                            'required'; } ?>>
                                                    </div>

                                                </div>

                                                <?php endforeach; ?>

                                                <h5> Label - Agent </h5>

                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-select mb-2 mt-2"
                                                            aria-label="select example" name="contractResult[5][]"
                                                            required multiple>
                                                            <option disabled>Select Label</option>

                                                            <?php
                                                            if (!connectToDB()) {
                                                                echo "Could not connect to DB";
                                                            }

                                                            $allLabels = executePlainSQL("select LABELNAME, AGENTNAME, AGENTID from AGENT order by LABELNAME");

                                                            while (($row = oci_fetch_assoc($allLabels)) != false):
                                                            ?>
                                                            <option
                                                                value="<?php echo $row["LABELNAME"] . " - " . $row["AGENTNAME"] . " - " . $row["AGENTID"] ?>">
                                                                <?php echo $row["LABELNAME"] . " - " . $row["AGENTNAME"] ?>
                                                            </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <h5> Band </h5>
                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-select mb-2 mt-2"
                                                            aria-label="select example" name="contractResult[6]"
                                                            required>
                                                            <option disabled>Select Band</option>

                                                            <?php
                                                            if (!connectToDB()) {
                                                                echo "Could not connect to DB";
                                                            }

                                                            $allBands = executePlainSQL("(select BANDNAME from BAND)");

                                                            while (($row = oci_fetch_assoc($allBands)) != false):
                                                            ?>
                                                            <option
                                                                value="<?php echo $row["BANDNAME"]?>">
                                                                <?php echo $row["BANDNAME"]; ?>
                                                            </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit button" name="request"
                                                        class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    function is_agent_in($key_agent, $agents) {
                        foreach($agents as $agent) {
                            if (trim($agent["AGENTID"]) === trim($key_agent["AGENTID"])) {
                                return true;
                            }
                        }
                        return false;
                    };
                    ?>
                    <?php foreach ($contracts as $contract): ?>
                    <div class="container card card-body mb-2">
                        <div class="row">

                            <div class="col">
                                <?php echo $contract["CONTRACTID"] ?>
                            </div>

                            <div class="col">
                                <a class="btn btn-primary float-end" data-bs-toggle="collapse"
                                    href="#<?php echo "dtdd" . $contract["CONTRACTID"] ?>" role="button"
                                    aria-expanded="false"
                                    aria-controls="<?php echo "dtdd" . $contract["CONTRACTID"] ?>">
                                    Details
                                </a>
                            </div>
                        </div>

                        <div class="collapse" id="<?php echo "dtdd" . $contract["CONTRACTID"] ?>">
                            <hr>
                            <div class="row d-flex">

                                <div class="col-8">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Field</th>
                                                <th scope="col">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">Contract ID</th>
                                                <td>
                                                    <?php echo $contract["CONTRACTID"] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Label Name</th>
                                                <td>
                                                    <?php echo $contract["LABELNAME"] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Band Name</th>
                                                <td colspan="2">
                                                    <?php echo $contract["BANDNAME"] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Signing Bonus</th>
                                                <td colspan="2">$
                                                    <?php echo $contract["SIGNINGBONUS"] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Royalty</th>
                                                <td colspan="2">
                                                    <?php echo $contract["ROYALTY"] ?>%
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Contract Duration</th>
                                                <td colspan="2">
                                                    <?php echo $contract["TIMEBASEDCONTRACTDURATION"] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Number of Contracted Albums</th>
                                                <td colspan="2">
                                                    <?php echo $contract["CONTENTBASEDNUMBEROFALBUMS"] ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-4">

                                    <ul class="list-group">
                                        <h4> Agents </h4>
                                        <?php foreach ($contract['AGENTS'] as $agent_list_item): ?>

                                        <li class="list-group-item">
                                            <?php echo $agent_list_item['AGENTNAME'] ?>
                                        </li>

                                        <?php endforeach; ?>

                                    </ul>
                                </div>
                            </div>

                            <div class="row d-flex justify-content-end">
                                <div class="col-md-auto">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#updateModal<?php echo $contract["CONTRACTID"] ?>">
                                        Update
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="updateModal<?php echo $contract["CONTRACTID"]; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $contract["CONTRACTID"]; ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel<?php echo $contract["CONTRACTID"]; ?>">Update Contract</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" id="editContractRequest" name="editContractRequest">

                                                <?php
                                                $contractFields = array(
                                                    "Contract ID:",
                                                    "Signing Bonus (USD):",
                                                    "Royalty:",
                                                    "Contract Duration:",
                                                    "Album Quota:",
                                                );

                                                $contractResult = array(
                                                    "CONTRACTID" => $contract['CONTRACTID'],
                                                    "SIGNINGBONUS" => $contract['SIGNINGBONUS'],
                                                    "ROYALTY" => $contract['ROYALTY'],
                                                    "TIMEBASEDCONTRACTDURATION" => $contract['TIMEBASEDCONTRACTDURATION'],
                                                    "CONTENTBASEDNUMBEROFALBUMS" => $contract['CONTENTBASEDNUMBEROFALBUMS'],
                                                    "AGENTS" => $contract['AGENTS'],
                                                    "BAND" => $contract['BANDNAME']
                                                );
                                                ?>

                                                <?php foreach ($contractFields as $key => $contractField): ?>

                                                <div class="form-group row pb-1">
                                                    <label for="<?php echo $contractField ?>"
                                                        class="col-sm-5 col-form-label">
                                                        <?php echo $contractField ?>
                                                    </label>

                                                    <div class="col-sm">
                                                        <input type="text" class="form-control"
                                                            name="contractResult[<?php echo $key ?>]"
                                                            value="<?php echo array_values($contractResult)[$key]; ?>"
                                                            id="<?php echo $contractField ?>" <?php if ($key < 3) { echo
                                                            'required'; } ?>
                                                            <?php if ($key == 0) {echo "readonly";} ?>>
                                                    </div>

                                                </div>

                                                <?php endforeach; ?>

                                                <h5> Label - Agent </h5>

                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-select mb-2 mt-2"
                                                            aria-label="select example" name="contractResult[5][]"
                                                            required multiple>
                                                            <option disabled>Select Label</option>

                                                            <?php
                                                            if (!connectToDB()) {
                                                                echo "Could not connect to DB";
                                                            } 

                                                            $allLabels = executePlainSQL("select AGENTID, AGENTNAME, LABELNAME from AGENT order by LABELNAME");

                                                            while (($row = oci_fetch_assoc($allLabels))):
                                                            ?>
                                                            <option 
                                                                value="<?php echo $row["LABELNAME"] . " - " . $row["AGENTNAME"] . " - " . $row["AGENTID"] ?>"
                                                                <?php if (is_agent_in($row, $contract["AGENTS"])) {echo "selected";} ?>>
                                                                <?php echo $row["LABELNAME"] . " - " . $row["AGENTNAME"] ?>
                                                            </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <h5> Band </h5>
                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-select mb-2 mt-2"
                                                            aria-label="select example" name="contractResult[6]"
                                                            required >
                                                            <option disabled>Select Band</option>

                                                            <?php
                                                            if (!connectToDB()) {
                                                                echo "Could not connect to DB";
                                                            }

                                                            $allBands = executePlainSQL("(select BANDNAME from BAND)");

                                                            while (($row = oci_fetch_assoc($allBands)) != false):
                                                            ?>
                                                            <option
                                                                value="<?php echo $row["BANDNAME"] ?>"
                                                                <?php if (trim($row["BANDNAME"]) === trim($contract["BANDNAME"])) {echo "selected";} ?>>
                                                                <?php echo $row["BANDNAME"]; ?>
                                                            </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit button" name="request"
                                                        class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                </div>
                                <div class="col-md-auto">
                                    <form method="post">
                                        <input type="hidden" name="deleteContractRequest" value="<?php echo $contract["CONTRACTID"] ; ?>">
                                        <button type="submit button" name="request" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </li>
        </ul>



    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
        </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
        </script>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <?php
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
    
    function debugAlertMessage($message)
    {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function handleInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insNo'],
            ":bind2" => $_POST['insName']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
        oci_commit($db_conn);
    }
    function test_input($new_contract)
    {
        $selected_labels = $new_contract[5];
        $temp_label = explode(" - ", $new_contract[5][0])[0];

        foreach ($selected_labels as $selected_label) {
            if (strcmp(explode(" - ", $selected_label)[0], $temp_label) != 0) {
                return -1;
            }
        }

        return 1;
    }

    function handleAddNewContract()
    {
        global $db_conn;
        $new_contract = $_POST['contractResult'];

        if (test_input($new_contract) == -1) {
            echo "All agents must be from same label!\n";
        } else {
    
            if (connectToDB()) {
                $test = executePlainSQL("select * from CONTRACT");
                while (($row = oci_fetch_assoc($test))) {
                    if (trim($new_contract[0]) === trim($row["CONTRACTID"])) {
                        echo "Failed to add new contract. Contract ID must be unique!";
                        return;
                    }
                }

                $contract_label = explode(" - ", $new_contract[5][0])[0];
                $contract_band = $new_contract[6];

                executePlainSQL("insert into CONTRACT values ('{$new_contract[0]}', '$contract_label', '$contract_band', '{$new_contract[1]}', '{$new_contract[2]}', '{$new_contract[3]}', '{$new_contract[4]}')");

                foreach ($new_contract[5] as $agent) {
                    $agent_exploded = explode(" - ", $agent);
                    executePlainSQL("insert into AGENTCONTRACTMANAGEMENT values ('{$agent_exploded[2]}', '{$new_contract[0]}')");
                }

                oci_commit($db_conn);
            }
        }
        unset($_POST['addContractRequest']);
        unset($_POST['request']);
    }

    function handleDeleteContract() {
        global $db_conn;
        $id_to_delete = $_POST['deleteContractRequest'];

        if (connectToDB()) {
            executePlainSQL("delete from CONTRACT where CONTRACTID = '$id_to_delete'");
            oci_commit($db_conn);
        }
        unset($_POST['deleteContractRequest']);
        unset($_POST['request']);
    }

    function handleEditContract() {
        global $db_conn;
        $current_contract = $_POST['contractResult'];

        if (test_input($current_contract) == -1) {
            echo "All agents must be from same label and all albums must be from same group!\n";
        } else {
    
            if (connectToDB()) {

                $contract_label = explode(" - ", $current_contract[5][0])[0];
                $contract_band = $current_contract[6];

                executePlainSQL("update CONTRACT set LABELNAME = '$contract_label', BANDNAME = '$contract_band', SIGNINGBONUS = '$current_contract[1]', ROYALTY = '$current_contract[2]', TIMEBASEDCONTRACTDURATION = '$current_contract[3]', CONTENTBASEDNUMBEROFALBUMS = '$current_contract[4]'");
                
                // drop all agents with this contract
                $test = executePlainSQL("select * from AGENTCONTRACTMANAGEMENT");
                while (($row = oci_fetch_assoc($test))) {
                    if (trim($current_contract[0]) === trim($row["CONTRACTID"])) {
                        executePlainSQL("delete from AGENTCONTRACTMANAGEMENT where CONTRACTID = '$current_contract[0]'");
                    }
                }

                // re-add all agents
                foreach ($current_contract[5] as $agent) {
                    $agent_exploded = explode(" - ", $agent);
                    executePlainSQL("insert into AGENTCONTRACTMANAGEMENT values ('$agent_exploded[2]', '$current_contract[0]')");
                }

                oci_commit($db_conn);
            }
        }
        unset($_POST['editContractRequest']);
        unset($_POST['request']);
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest()
    {
        if (array_key_exists('addContractRequest', $_POST)) {
            handleAddNewContract();
        } else if (array_key_exists('deleteContractRequest', $_POST)) {
            handleDeleteContract();
        } else if (array_key_exists('editContractRequest', $_POST)) {
            handleEditContract();
        }
        disconnectFromDB();
    }

    if (isset($_POST['request'])) {
        handlePOSTRequest();
    }
    ?>
</body>

</html>