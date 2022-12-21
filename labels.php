<!doctype html>
<html lang="en">

<head>
    <title>Labels</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
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
                            <a class="nav-link active" href="labels.php">Labels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php">Groups</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contracts.php">Contracts</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container mt-3">
            <div class="d-flex align-items-center">
                <h1 class="display-6 me-5">Labels owned:</h1>
                <a class="btn btn-md btn-success" data-bs-toggle="modal" href="#addModal" role="button">Add Label</a>
                <form action="labels.php" method="GET">
                    <button class="btn btn-sm btn-info" name="agentCountry">
                        Show agent count by country
                    </button>
                    <button class="btn btn-sm btn-info" name="agentAlbums">
                        Show Agents handling content based contracts
                    </button>
                    <input type="hidden" name="labelName" value="<?php echo $labelName ?>">
                    <button name="mainStreetContract" type="s ubmit" class="btn btn-info">
                        Find 1 Main street contracts
                    </button>
                </form>
                <div class="modal fade" id="addModal" aria-hidden="true" aria-labelledby="addModalToggleLabel"
                    tabindex="-1">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addModalToggleLabel">Add a new Label</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="labels.php">
                                    <input type="hidden" id="addLabelRequest" name="addLabelRequest">
                                    <div class="row">
                                        <div class="col">
                                            <p class="lead">Label metadata</p>
                                            <div class="mb-3">
                                                <label for="labelName" class="form-label">Label Name</label>
                                                <input type="text" class="form-control" id="labelName" name="labelName">
                                                <div id="labelNameHelp" class="form-text">This cannot be changed later
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Country</label>
                                                <input type="text" class="form-control" id="country" name="country">
                                            </div>
                                            <div class="mb-3">
                                                <label for="provinceOrState" class="form-label">Province/State</label>
                                                <input type="text" class="form-control" id="provinceOrState"
                                                    name="provinceOrState">
                                            </div>
                                            <div class="mb-3">
                                                <label for="streetNumber" class="form-label">Street Number</label>
                                                <input type="text" class="form-control" id="streetNumber"
                                                    name="streetNumber">
                                            </div>
                                            <div class="mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city" name="city">
                                            </div>
                                            <div class="mb-3">
                                                <label for="postalCode" class="form-label">Postal Code</label>
                                                <input type="text" class="form-control" id="postalCode"
                                                    name="postalCode">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <p class="lead">Add new agents</p>
                                                    <div class="mb-3">
                                                        <label for="newAgentNames" class="form-label">Agent
                                                            Names</label>
                                                        <textarea class="form-control" placeholder="New agents"
                                                            id="newAgentNames" name="newAgentNames"></textarea>
                                                        <div id="newAgentHelp" class="form-text">Comma seperated
                                                            list of agent names to add</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" name="addNewLabel"
                                                class="btn btn-success">Create</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            require __DIR__ . "/sql_functions.php";

            if (!connectToDB()) {
                echo "Could not connect to DB";
                return;
            }

            $labelQueryResult = executePlainSQL("
                select * from label
                natural join addresslookup
                order by labelname asc"
            );

            $labels = [];
            while (($labelRow = oci_fetch_assoc($labelQueryResult)) != false) {
                $labels["{$labelRow['LABELNAME']}"] = [
                    "STREETNUMBER" => $labelRow['STREETNUMBER'],
                    "POSTALCODE" => $labelRow['POSTALCODE'],
                    "CITY" => $labelRow['CITY'],
                    "PROVINCEORSTATE" => $labelRow['PROVINCEORSTATE'],
                    "COUNTRY" => $labelRow['COUNTRY'],
                    "AGENTS" => [],
                    "CONTRACTS" => []
                ];

                // get all the agents in this label
                $agentsInLabelQueryResult = executePlainSQL("
                    select agentname, agentid from agent where labelname='{$labelRow['LABELNAME']}'
                ");

                while (($agentRow = oci_fetch_assoc($agentsInLabelQueryResult)) != false) {
                    $labels[$labelRow['LABELNAME']]["AGENTS"]["{$agentRow['AGENTID']}"] = $agentRow['AGENTNAME'];
                }

                // get all the contracts in this label
                $contractsInLabelQueryResult = executePlainSQL("
                    select contractid, bandname, signingbonus, royalty, timebasedcontractduration, contentbasednumberofalbums from contract where labelname='{$labelRow["LABELNAME"]}'
                ");

                while (($contractRow = oci_fetch_assoc($contractsInLabelQueryResult)) != false) {
                    $labels[$labelRow['LABELNAME']]["CONTRACTS"]["{$contractRow['CONTRACTID']}"] = [
                        "BANDNAME" => $contractRow['BANDNAME'],
                        "SIGNINGBONUS" => $contractRow["SIGNINGBONUS"],
                        "ROYALTY" => $contractRow["ROYALTY"],
                        "TIMEBASEDCONTRACTDURATION" => $contractRow["TIMEBASEDCONTRACTDURATION"],
                        "CONTENTBASEDNUMBEROFALBUMS" => $contractRow["CONTENTBASEDNUMBEROFALBUMS"]
                    ];
                }
            }
            
            ?>
            <?php foreach ($labels as $labelName => $labelData): ?>
            <div class="accordion" id="labelAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header"
                        id="<?php echo "accordion". str_replace(" ", "", $labelName) ?>Heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#<?php echo "accordion". str_replace(" ", "", $labelName) ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo "accordion". str_replace(" ", "", $labelName) ?>">
                            <?php echo $labelName ?>
                        </button>
                    </h2>
                    <div id="<?php echo "accordion". str_replace(" ", "", $labelName) ?>"
                        class="accordion-collapse collapse"
                        aria-labelledby="<?php echo "accordion". str_replace(" ", "", $labelName) ?>Heading"
                        data-bs-parent="#labelAccordion">
                        <div class="accordion-body">
                            <div class='card card-body'>
                                <?php echo $labelData['STREETNUMBER'] ?>
                                <?php echo $labelData['POSTALCODE'] ?>
                                <?php echo $labelData['CITY'] ?>
                                <?php echo $labelData['PROVINCEORSTATE'] ?>
                                <?php echo $labelData['COUNTRY'] ?>
                            </div>
                            <div class='container text-center mt-3'>
                                <div class='row'>
                                    <div class='col'>
                                        <h2 class='sub-header'>Agents</h2>
                                        <div class='table-responsive'>
                                            <table class='table table-hover'>
                                                <thead>
                                                    <tr>
                                                        <th class='col-md-2'>Agent ID</th>
                                                        <th class='col-md-3'>Agent Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($labelData['AGENTS'] as $agentId => $agentName): ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $agentId ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $agentName ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class='col'>
                                        <form action="labels.php" method="GET">
                                            <input type="hidden" name="labelName" value="<?php echo $labelName ?>">
                                            <h2 class='sub-header'>Contracts</h2>
                                            <div class="d-flex flex-column align-items-start">
                                                <p class="lead">Select attributes to return</p>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" checked disabled>
                                                    <label for="contractAttributes" class="form-label">
                                                        Contract ID
                                                    </label>
                                                </div>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" value="bandname">
                                                    <label for="contractAttributes" class="form-label">
                                                        Band Name
                                                    </label>
                                                </div>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" value="signingbonus">
                                                    <label for="contractAttributes" class="form-label">
                                                        Signing Bonus
                                                    </label>
                                                </div>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" value="royalty">
                                                    <label for="contractAttributes" class="form-label">
                                                        Royalty
                                                    </label>
                                                </div>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" value="timebasedcontractduration">
                                                    <label for="contractAttributes" class="form-label">
                                                        Duration of contract (If applicable)
                                                    </label>
                                                </div>
                                                <div>
                                                    <input type="checkbox" name="contractAttributes[]"
                                                        id="contractAttributes" value="contentbasednumberofalbums">
                                                    <label for="contractAttributes" class="form-label">
                                                        Number of albums in contract (If applicable)
                                                    </label>
                                                </div>
                                            </div>
                                            <button name="fetchContracts" class="btn btn-sm btn-success">
                                                Fetch contracts for
                                                <?php echo $labelName ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button data-bs-toggle="modal"
                                    data-bs-target="#update<?php echo str_replace(' ', '', $labelName) ?>" type="button"
                                    class="btn btn-warning me-2">Update</button>
                                <div class="modal fade" id="update<?php echo str_replace(' ', '', $labelName) ?>"
                                    tabindex="-1" aria-labelledby="update<?php echo str_replace(' ', '', $labelName) ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="update<?php echo str_replace(' ', '', $labelName) ?>">
                                                    Update
                                                    <?php echo $labelName ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="labels.php">
                                                <div class="modal-body">
                                                    <input type="hidden" id="editLabelRequest" name="editLabelRequest">
                                                    <div class="row">
                                                        <div class="col">
                                                            <p>Label metadata</p>
                                                            <input type="hidden" name="labelName"
                                                                value="<?php echo trim($labelName) ?>">
                                                            <div class="mb-3">
                                                                <label for="labelName" class="form-label">Label
                                                                    Name</label>
                                                                <input disabled value="<?php echo trim($labelName) ?>"
                                                                    type="text" class="form-control" id="labelName">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="country" class="form-label">Country</label>
                                                                <input value="<?php echo trim($labelData['COUNTRY']) ?>"
                                                                    type="text" class="form-control" id="country"
                                                                    name="country">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="provinceOrState"
                                                                    class="form-label">Province/State</label>
                                                                <input
                                                                    value="<?php echo trim($labelData['PROVINCEORSTATE']) ?>"
                                                                    type="text" class="form-control"
                                                                    id="provinceOrState" name="provinceOrState">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="streetNumber" class="form-label">Street
                                                                    Number</label>
                                                                <input
                                                                    value="<?php echo trim($labelData['STREETNUMBER']) ?>"
                                                                    type="text" class="form-control" id="streetNumber"
                                                                    name="streetNumber">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label">City</label>
                                                                <input value="<?php echo trim($labelData['CITY']) ?>"
                                                                    type="text" class="form-control" id="city"
                                                                    name="city">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="postalCode" class="form-label">Postal
                                                                    Code</label>
                                                                <input
                                                                    value="<?php echo trim($labelData['POSTALCODE']) ?>"
                                                                    type="text" class="form-control" id="postalCode"
                                                                    name="postalCode">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <p class="lead">Remove agents</p>
                                                                    <div class="mb-3">
                                                                        <label for="existingAgentNames"
                                                                            class="form-label">Agent
                                                                            Names</label>
                                                                        <?php foreach($labelData['AGENTS'] as $agentId => $agentName): ?>
                                                                        <div class="m-1">
                                                                            <input type="checkbox"
                                                                                name="existingAgentIds[]"
                                                                                id="existingAgentIds"
                                                                                value="<?php echo $agentId ?>">
                                                                            <label for="existingAgentIds"
                                                                                class="form-label">
                                                                                Name:
                                                                                <?php echo $agentName ?>, ID:
                                                                                <?php echo $agentId ?>
                                                                            </label>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                        <div id="existingAgentHelp" class="form-text">
                                                                            Warning: If checked, that agent will be
                                                                            removed</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <p class="lead">Add new agents</p>
                                                                    <div class="mb-3">
                                                                        <div class="mb-3">
                                                                            <label for="newAgentNames"
                                                                                class="form-label">Agent
                                                                                Names</label>
                                                                            <textarea class="form-control"
                                                                                placeholder="New agents"
                                                                                id="newAgentNames"
                                                                                name="newAgentNames"></textarea>
                                                                            <div id="newAgentHelp" class="form-text">
                                                                                Comma seperated
                                                                                list of agent names to add</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="editLabel" type="button"
                                                        class="btn btn-primary">Save
                                                        changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <button data-bs-toggle="modal"
                                    data-bs-target="#delete<?php echo str_replace(' ', '', $labelName) ?>" type="button"
                                    class="btn btn-danger">Delete</button>
                                <div class="modal fade" id="delete<?php echo str_replace(' ', '', $labelName) ?>"
                                    tabindex="-1" aria-labelledby="delete<?php echo str_replace(' ', '', $labelName) ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="delete<?php echo str_replace(' ', '', $labelName) ?>">
                                                    Delete
                                                    <?php echo $labelName ?> ?
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="labels.php">
                                                <input type="hidden" id="deleteLabelRequest" name="deleteLabelRequest">
                                                <input type="hidden" name="labelName" value=<?php echo $labelName ?> >
                                                <div class=" modal-body">
                                                    <p class="lead">This cannot be undone, are you sure? This will also
                                                        delete all the associated agents</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="deleteLabel" type="button"
                                                        class="btn btn-danger">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

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

    function handleAddNewLabel() {
        //Getting the values from user and insert data into the table
    
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }
        $labelName = $_POST['labelName'];
        $country = $_POST['country'];
        $streetNumber = $_POST['streetNumber'];
        $postalCode = $_POST['postalCode'];
        $city = $_POST['city'];
        $provinceOrState = $_POST['provinceOrState'];
        $newAgents = explode(',', $_POST['newAgentNames']);

        $tuple = [
            ":bind1" => $labelName,
            ":bind2" => $country,
            ":bind3" => $streetNumber,
            ":bind4" => $postalCode,
            ":bind5" => $city,
            ":bind6" => $provinceOrState,
        ];

        // check if we already have this address
        $addressLookupResult = executePlainSQL(
            "select * from ADDRESSLOOKUP where COUNTRY='{$country}' and POSTALCODE='{$postalCode}'"
        );

        $row = oci_fetch_row($addressLookupResult);

        if (!isset($row) || empty($row)) {
            // need to add this addresslookup value
            executeBoundSQL(
                "insert into ADDRESSLOOKUP (COUNTRY, POSTALCODE, CITY, PROVINCEORSTATE) values (:bind2, :bind4, :bind5, :bind6)",
                [$tuple]
            );
        }

        // add new label
        executeBoundSQL(
            "insert into LABEL (LABELNAME, COUNTRY, STREETNUMBER, POSTALCODE) values (:bind1, :bind2, :bind3, :bind4)",
            [$tuple]
        );

        // add new agents
        foreach ($newAgents as $agentName) {
            $agentId = uniqid();
            $tuple[':bind7'] = $agentId;
            $tuple[':bind8'] = trim($agentName);
            executeBoundSQL(
                "insert into AGENT (AGENTID, AGENTNAME, LABELNAME) values (:bind7, :bind8, :bind1)",
                [$tuple]
            );
        }

        return;
    }

    function handleUpdateLabel() {    
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }
        $labelName = $_POST['labelName'];
        $country = $_POST['country'];
        $streetNumber = $_POST['streetNumber'];
        $postalCode = $_POST['postalCode'];
        $city = $_POST['city'];
        $provinceOrState = $_POST['provinceOrState'];
    
        $existingAgentIdsFromForm = $_POST['existingAgentIds'];

        $existingAgentIds = [];
        if(isset($existingAgentIdsFromForm) && !empty($existingAgentIdsFromForm)) {
            $existingAgentIds = $existingAgentIdsFromForm;
        }

        $newAgentsFromForm = $_POST['newAgentNames'];
        $newAgents = [];
        if (isset($newAgentsFromForm) && !empty($newAgentsFromForm)) {
            $newAgents = explode(",", $newAgentsFromForm);
        }

        $tuple = [
            ":bind1" => $labelName,
            ":bind2" => $country,
            ":bind3" => $streetNumber,
            ":bind4" => $postalCode,
            ":bind5" => $city,
            ":bind6" => $provinceOrState,
        ];

        // check if we already have this address
        $addressLookupResult = executePlainSQL(
            "select * from ADDRESSLOOKUP where COUNTRY='{$country}' and POSTALCODE='{$postalCode}'"
        );

        $row = oci_fetch_row($addressLookupResult);
        
        if (!isset($row) || empty($row)) {
            executeBoundSQL(
                "insert into ADDRESSLOOKUP (COUNTRY, POSTALCODE, CITY, PROVINCEORSTATE) values (:bind2, :bind4, :bind5, :bind6)",
                [$tuple]
            );
        }

        // add new label
        $labelUpdateResult = executePlainSQL(
            "update LABEL set COUNTRY='{$country}', STREETNUMBER='{$streetNumber}', POSTALCODE='{$postalCode}' where labelname='{$labelName}'"
        );

        $row = oci_fetch_row($labelUpdateResult);

        // check if agent exists and delete if they do
        if (sizeof($existingAgentIds) != 0 && isset($existingAgentIds) && !empty($existingAgentIds)) {
            foreach ($existingAgentIds as $agentId) {
                $agentookupResult = executePlainSQL(
                    "select agentid from agent where agentid='{$agentId}'"
                );

                $row = oci_fetch_row($agentookupResult);

                if (!isset($row) || empty($row)) {
                    // agent does not exist
                    echo "Invalid existing agent entry";
                    return;
                }

                $tuple[':bind7'] = $agentId;

                executePlainSQL("delete from agent where agentid='{$agentId}'");
            }
        }

        // add new agents
        if (sizeof($newAgents) != 0 && isset($newAgents) && !empty($newAgents)) {
            foreach ($newAgents as $agentName) {
                $agentId = uniqid();
                $tuple[':bind7'] = $agentId;
                $tuple[':bind8'] = trim($agentName);
                executeBoundSQL(
                    "insert into AGENT (AGENTID, AGENTNAME, LABELNAME) values (:bind7, :bind8, :bind1)",
                    [$tuple]
                );
            }
        }
        return;
    }

    function handleDeleteLabel() {
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }

        $labelName = $_POST['labelName'];

        $labelLookupResult = executePlainSQL(
            "select * from label where labelname='{$labelName}'"
        );

        $row = oci_fetch_row($labelLookupResult);

        if (!isset($row) || empty($row)) {
            // agent does not exist
            echo "Invalid existing label entry";
            return;
        }

        executePlainSQL("delete from label where labelname='{$labelName}'");
    }

    function handleDivisionRequest() {
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }
        
        $divisionResult = executePlainSQL("
            SELECT contractid FROM agentcontractmanagement sx WHERE NOT EXISTS ( 
                (SELECT agentid FROM 
                (select agentid from agent natural join label where streetnumber='1 Main Street')
                ) MINUS 
                (SELECT sp.agentid FROM agentcontractmanagement sp WHERE sp.contractid = sx.contractid ) 
            )
        ");
        
        echo "<h5 class='sub-header'>Contracts that are managed by ALL agents on 1 Main Street</h5>";
        echo "<div class='d-flex flex-column align-items-start'>";

        while (($row = oci_fetch_assoc($divisionResult)) != false) {
            echo "<p>{$row['CONTRACTID']}</p>";
        }

        return;
    }

    function handleProjectionRequest() {
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }

        $labelName = $_GET['labelName'];
        $contractAttributesFromForm = $_GET['contractAttributes'];

        $contractAttributes = "contractid"; // always select contract id
        if(isset($contractAttributesFromForm) && !empty($contractAttributesFromForm)) {
            $contractAttributes = $contractAttributes . "," . implode(",", $contractAttributesFromForm);
        }
        
        echo "
        <h2 class='sub-header'>Contracts for {$labelName}</h2>
            <div class='table-responsive'>
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th class='col-md-2'>Contract ID</th>";
                            if(in_array("bandname", $contractAttributesFromForm)) {
                                echo "<th class='col-md-3'>Band Name</th>";
                            }
                            if(in_array("signingbonus", $contractAttributesFromForm)) {
                                echo "<th class='col-md-3'>Signing Bonus</th>";
                            }
                            if(in_array("royalty", $contractAttributesFromForm)) {
                                echo "<th class='col-md-3'>Royalty</th>";
                            }
                            if(in_array("timebasedcontractduration", $contractAttributesFromForm)) {
                                echo "<th class='col-md-3'>Duration of contract</th>";
                            }
                            if(in_array("contentbasednumberofalbums", $contractAttributesFromForm)) {
                                echo "<th class='col-md-3'>Number of albums</th>";
                            }
                        echo "
                        </tr>
                    </thead>
                <tbody>";

        $contractsInLabelQueryResult = executePlainSQL("
                select " . $contractAttributes .  " from contract where labelname='{$labelName}'
        ");

        while (($contractRow = oci_fetch_assoc($contractsInLabelQueryResult)) != false) {
            echo "<tr>";
            echo "<th class='col-md-3'>{$contractRow['CONTRACTID']}</th>";
            if(in_array("bandname", $contractAttributesFromForm)) {
                echo "<th class='col-md-3'>{$contractRow['BANDNAME']}</th>";
            }
            if(in_array("signingbonus", $contractAttributesFromForm)) {
                echo "<th class='col-md-3'>{$contractRow["SIGNINGBONUS"]}</th>";
            }
            if(in_array("royalty", $contractAttributesFromForm)) {
                echo "<th class='col-md-3'>{$contractRow["ROYALTY"]}</th>";
            }
            if(in_array("timebasedcontractduration", $contractAttributesFromForm)) {
                echo "<th class='col-md-3'>{$contractRow["TIMEBASEDCONTRACTDURATION"]}</th>";
            }
            if(in_array("contentbasednumberofalbums", $contractAttributesFromForm)) {
                echo "<th class='col-md-3'>{$contractRow["CONTENTBASEDNUMBEROFALBUMS"]}</th>";
            }
            echo "
            </tr>
            ";
        }
        echo "
        </tbody>
        </table>
    </div>
        ";
        return;
    }

    function handleGroupByAggregationRequest() {
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }

        $agentCountryResult = executePlainSQL("
            select country, count(agentid) agentCount from agent natural join label group by country
        ");

        echo "
        <h2 class='sub-header'>Agent count by country</h2>
            <div class='table-responsive'>
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th class='col-md-3'>Country</th>
                            <th class='col-md-3'>Agent count</th>
                        </tr>
                    </thead>
                <tbody>";

        while (($row = oci_fetch_assoc($agentCountryResult)) != false) {
            echo "<tr>";
            echo "<th class='col-md-3'>{$row['COUNTRY']}</th>";
            echo "<th class='col-md-3'>{$row['AGENTCOUNT']}</th>";
            echo "
            </tr>
            ";
        }
        echo "
        </tbody>
        </table>
    </div>
        ";

        return;
    }

    function handleGroupByHavingRequest() {
        if (!connectToDB()) {
            echo "Could not connect to DB";
            return;
        }

        $agentCountryResult = executePlainSQL("
            select a.agentid from agentcontractmanagement a 
            inner join contract c 
            on c.contractid=a.contractid 
            group by a.agentid having min(c.contentbasednumberofalbums)>1
        ");

        echo "<h5 class='sub-header'>Agent IDs of agents that handle content based albums</h5>";
        echo "<div class='d-flex flex-column align-items-start'>";
        while (($row = oci_fetch_assoc($agentCountryResult)) != false) {
            echo "<p>{$row['AGENTID']}</p>";
        }
        echo "</div>";

        return;
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest()
    {
        if (array_key_exists('addLabelRequest', $_POST)) {
            handleAddNewLabel();
        } else if (array_key_exists('editLabelRequest', $_POST)) {
            handleUpdateLabel();
        } else if (array_key_exists('deleteLabelRequest', $_POST)) {
            handleDeleteLabel();
        }

        disconnectFromDB();
    }

    function handleGetRequest()
    {
        if (array_key_exists('mainStreetContract', $_GET)) {
            handleDivisionRequest();
        } else if (array_key_exists('fetchContracts', $_GET)) {
            handleProjectionRequest();
        } else if (array_key_exists('agentCountry', $_GET)) {
            handleGroupByAggregationRequest();
        } else if (array_key_exists('agentAlbums', $_GET)) {
            handleGroupByHavingRequest();
        }

        disconnectFromDB();
    }

    if (isset($_POST['addNewLabel']) || isset($_POST['editLabel']) || isset($_POST['deleteLabel'])) {
        handlePOSTRequest();
    } else if (isset($_GET['mainStreetContract']) || isset($_GET['fetchContracts']) || isset($_GET['agentCountry']) || isset($_GET['agentAlbums'])) {
        handleGetRequest();
    }
    ?>
</body>

</html>