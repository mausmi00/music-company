<!doctype html>
<html lang="en">

<head .scroller { overflow: scroll; padding: 5px; height: 100%; }>
    <title>Dashboard</title>
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
        <?php
                        require __DIR__ . "/sql_functions.php";
                        
                        if (connectToDB()) {
                            $bandsAndArtistsQuery = executePlainSQL("select * from BAND
                                                                        natural join ARTIST
                                                                        natural join MEMBEROF");
                            $bandsAndArtists = [];
                            $artistsArray = [];
                            // key bandname
                            while ($row = oci_fetch_assoc($bandsAndArtistsQuery)) {
                                $artistsArray["{$row["ARTISTNAME"]}"] = $row['ARTISTNAME'];
                                if (empty($bandsAndArtists[$row['BANDNAME']])) {
                                    $bandsAndArtists[$row["BANDNAME"]] =
                                    array(
                                        array(
                                            "ARTISTNAME" => $row["ARTISTNAME"],
                                            "DATEOFBIRTH" => $row["DATEOFBIRTH"],
                                        ),
                                    );
                                } else {
                                    array_push(
                                        $bandsAndArtists[$row["BANDNAME"]],
                                        array(
                                            "ARTISTNAME" => $row["ARTISTNAME"],
                                            "DATEOFBIRTH" => $row["DATEOFBIRTH"],
                                        )
                                    );
                                }
                            }
                            ;
                            $bandsAndAlbumQuery = executePlainSQL("select * from BAND
                                                                        natural join CONTRIBUTESTO
                                                                        natural join ALBUM
                                                                        left join REVENUELOOKUP on REVENUELOOKUP.GENRE = ALBUM.GENRE and REVENUELOOKUP.EXPECTEDSALES = ALBUM.EXPECTEDSALES"
                            );
                            $bandsAndAlbum = [];
                            $albums =[];
                            while ($row = oci_fetch_assoc($bandsAndAlbumQuery)) {
                                $albums["{$row["ALBUMID"]}"] = $row['ALBUMID'];
                                if (empty($bandsAndAlbum[$row['BANDNAME']])) {
                                    $bandsAndAlbum[$row["BANDNAME"]] = array(
                                        array(
                                            "ALBUMID" => $row["ALBUMID"],
                                            "LABELNAME" => $row["LABELNAME"],
                                            "TITLE" => $row["TITLE"],
                                            "RELEASEDATE" => $row["RELEASEDATE"],
                                            "GENRE" => $row["GENRE"],
                                            "NUMBERSOLD" => $row["NUMBERSOLD"],
                                            "EXPECTEDSALES" => $row["EXPECTEDSALES"],
                                            "PRICE" => $row["PRICE"]
                                        ),
                                    );
                                } else {
                                    array_push(
                                        $bandsAndAlbum[$row["BANDNAME"]],
                                        array(
                                            "ALBUMID" => $row["ALBUMID"],
                                            "LABELNAME" => $row["LABELNAME"],
                                            "TITLE" => $row["TITLE"],
                                            "RELEASEDATE" => $row["RELEASEDATE"],
                                            "GENRE" => $row["GENRE"],
                                            "NUMBERSOLD" => $row["NUMBERSOLD"],
                                            "EXPECTEDSALES" => $row["EXPECTEDSALES"],
                                            "PRICE" => $row["PRICE"]
                                        )
                                    );

                                }
                            }
                            $bandsAndShows = [];
                            $shows = [];
                            $bandsAndShowsQuery = executePlainSQL("select * from BAND
                                                                        natural join PERFORMS
                                                                        natural join MUSICSHOW
                                                                        left join SHOWREVENUELOOKUP on SHOWREVENUELOOKUP.TICKETSSOLD = MUSICSHOW.TICKETSSOLD and SHOWREVENUELOOKUP.COSTPERTICKET = MUSICSHOW.COSTPERTICKET"
                            );
                            while ($row = oci_fetch_assoc($bandsAndShowsQuery)) {
                                $venueAndShowDate = $row["VENUE"] . "/" . $row["SHOWDATE"];
                                array_push($shows, $venueAndShowDate);
                                $shows = array_unique($shows);
                                if (empty($bandsAndShows[$row['BANDNAME']])) {
                                    $bandsAndShows[$row["BANDNAME"]] = array(
                                        array(
                                            "VENUE" => $row["VENUE"],
                                            "SHOWDATE" => $row["SHOWDATE"],
                                            "TICKETSSOLD" => $row["TICKETSSOLD"],
                                            "COSTPERTICKET" => $row["COSTPERTICKET"],
                                            "REVENUE" => $row["REVENUE"]
                                        ),
                                    );
                                } else {
                                    array_push(
                                        $bandsAndShows[$row["BANDNAME"]],
                                        array(
                                            "VENUE" => $row["VENUE"],
                                            "SHOWDATE" => $row["SHOWDATE"],
                                            "TICKETSSOLD" => $row["TICKETSSOLD"],
                                            "COSTPERTICKET" => $row["COSTPERTICKET"],
                                            "REVENUE" => $row["REVENUE"]
                                        )
                                    );
                                }
                            }
                            $bandsAndTour = [];
                            $tours = [];
                            $bandsAndTourQuery = executePlainSQL("select * from BAND
                                                                        natural join GOESON
                                                                        natural join TOUR"
                            );
                            while ($row = oci_fetch_assoc($bandsAndTourQuery)) {
                                $tourAndStartDate = $row["TOURNAME"] . "/" . $row["STARTDATE"];
                                array_push($tours, $tourAndStartDate);
                                $tours = array_unique($tours);
                                if (empty($bandsAndTour[$row['BANDNAME']])) {
                                    $bandsAndTour[$row["BANDNAME"]] = array(
                                        array(
                                            "TOURNAME" => $row["TOURNAME"],
                                            "STARTDATE" => $row["STARTDATE"],
                                            "ENDDATE" => $row["ENDDATE"],

                                        ),
                                    );
                                } else {
                                    array_push(
                                        $bandsAndTour[$row["BANDNAME"]],
                                        array(
                                            "TOURNAME" => $row["TOURNAME"],
                                            "STARTDATE" => $row["STARTDATE"],
                                            "ENDDATE" => $row["ENDDATE"],
                                        )
                                    );
                                }
                            }
                            $albumSongs = [];
                            $albumSongsQuery = executePlainSQL("select * from song"
                            );
                            while ($row = oci_fetch_assoc($albumSongsQuery)) {
                                if (empty($albumSongs[$row['ALBUMID']])) {
                                    $albumSongs[$row["ALBUMID"]] = array(
                                        array(
                                            "TITLE" => $row["TITLE"],
                                            "TRACKLISTPOSITION" => $row["TRACKLISTPOSITION"],
                                            "DURATION" => $row["DURATION"]
                                        ),
                                    );
                                } else {
                                    array_push(
                                        $albumSongs[$row["ALBUMID"]],
                                        array(
                                            "TITLE" => $row["TITLE"],
                                            "TRACKLISTPOSITION" => $row["TRACKLISTPOSITION"],
                                            "DURATION" => $row["DURATION"]
                                        )
                                    );
                                }
                            }
                        }

                    ?>


        <ul class="list-group list-group-flush">

            <li class="list-group-item justify-content-evenly align-items-start">
                <div class="container">

                    <div class="row">
                        <div class="col">
                            <h1> Groups </h1>
                        </div>

                        <div class="col">
                            <button type="button" class="btn btn-light float-end" data-bs-toggle="modal"
                                    data-bs-target="#userBandInput">
                                    Top Bands
                            </button>
                            <div class="modal fade" id="userBandInput" tabindex="-1" aria-labelledby="userBandInput"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="userBandInput">Top Bands</h4>
                                                
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                </button>
                                            </div>
                                            <form method="POST" action="groups.php">
                                                    <input type="hidden" id="processUserBandInputQuery"
                                                    name="processUserBandInputQuery">
                                            <div class="modal-body">
                                                
                                                    <div class="row">
                                                        <div class="col">
                                                            A list of all the bands where the expected sales was more than 
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4"> 
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Value" name="salesCondition">
                                                        </div>
                                                    </div>
                                                    <small id="groupCreationHelp"
                                                                    class="form-text text-muted">Please enter a numerical value</small>
                                                                    
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="processUserBandInput" class="btn btn-primary">Get Result</button>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>


                        <div class="col">
                            <button type="button" class="btn btn-light float-end" data-bs-toggle="modal"
                                    data-bs-target="#userInput">
                                    User Selection
                            </button>
                            <div class="modal fade" id="userInput" tabindex="-1" aria-labelledby="userInput"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="addGroups">User Selection</h4>
                                                
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                </button>
                                            </div>
                                            <form method="POST" action="groups.php">
                                                    <input type="hidden" id="processUserInputQuery"
                                                    name="processUserInputQuery">
                                            <div class="modal-body">
                                                
                                                    <div class="row">
                                                        <div class="col">
                                                            SELECT
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Select Attribute" name="selectAttribute">
                                                        </div>
                                                        <div class="col">
                                                            FROM 
                                                        </div>
                                                        <div class="col-md-4"> 
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Table Name" name="selectTable">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            WHERE
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Condition#1" name="equalCondition">
                                                        </div>
                                                        <div class="col">
                                                            = 
                                                        </div>
                                                        <div class="col-md-4"> 
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Condition#1 Value" name="condition1Value">
                                                        </div>
                                                        <div class="col">
                                                        AND
                                                        </div>
                                                        <div class="col-md-4"> 
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Condition#2" name="compareCondition">
                                                        </div>
                                                        <div class="col">
                                                        =
                                                        </div>
                                                        <div class="col-md-4"> 
                                                            <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Condition#2 Value" name="condition2Value">
                                                        </div>
                                                    </div>
                                                    <small id="groupCreationHelp"
                                                                    class="form-text text-muted">Please enter non numerical values</small>
                                               
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="processUserInput" class="btn btn-primary">Get Result</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col">
                            <button type="button" class="btn btn-light float-end" data-bs-toggle="modal"
                                    data-bs-target="#nestedAggWithGroupBy">
                                    Top Title & Genre
                            </button>
                            <div class="modal fade" id="nestedAggWithGroupBy" tabindex="-1" aria-labelledby="nestedAggWithGroupBy"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="addGroups">Top Title & Genre</h4>
                                                
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                </button>
                                            </div>
                                            
                                            <div class="modal-body">
                                            <small id="groupCreationHelp"
                                                                    class="form-text text-muted">Title & genre of albums whose sales were greater than the avg expected sales of all other albums when grouped by title and genre
                                            </small>
                                                <table class="table" style="height:10px">
                                                    <thead>
                                                        <th scope="col">Title</th>
                                                        <th scope="col">Genre</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $nestedAggVal = getNestedAggVal();
                                                        foreach($nestedAggVal as $val):
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $val["TITLE"] ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $val["GENRE"] ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        
                        <div class="col">

                            <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Add Groups
                            </button>

                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="addGroups">Add Groups</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="groups.php">
                                                <!--?php $rand=rand(); $_SESSION['rand']=$rand;?-->

                                                <input type="hidden" id="addNewGroupQueryrequest"
                                                    name="addNewGroupQueryrequest">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">

                                                            <h6 class="mt-3" for="GroupNameLabel">Group Name</h6>

                                                            <input type="text" class="form-control" id="groupName"
                                                                name="groupName" placeholder="Group Name">
                                                        </div>
                                                        <div class="form-group">
                                                            <small id="groupCreationHelp"
                                                                class="form-text text-muted">You need to assign at least
                                                                one artist to a group.</small>
                                                            <h6 class="mt-3" for="artistSelection">Select from existing
                                                                artists</h6>

                                                            <div class="d-flex flex-row">

                                                                <div
                                                                    style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                    <?php foreach($artistsArray as $artist): ?>
                                                                    <div class="m-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="existingArtistSelect[]"
                                                                            id="<?php echo $artist ?>"
                                                                            value=" <?php echo $artist?>">
                                                                        <label class="form-check-label"
                                                                            for="flexCheckDefault">
                                                                            <?php echo $artist ?>
                                                                        </label>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="newArtistNamesLabel">Add a new
                                                                artist</h6>
                                                            <div>
                                                                <label>Artist Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="newArtistName" name="newArtistName"
                                                                    placeholder="Artist Name">
                                                            </div>
                                                            <div>
                                                                <label>Date of birth</label>
                                                                <input type="date" class="form-control" id="DOB"
                                                                    name="DOB" placeholder="01-JAN-2000">
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col">


                                                        <h5 for="TourNameLabel">Add Tours</h5>

                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="artistSelection">Select from existing
                                                                tours</h6>

                                                            <div class="d-flex flex-row">

                                                                <div
                                                                    style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                    <?php foreach($tours as $tour): ?>
                                                                    <?php $tour ?>
                                                                    <div class="m-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="existingTours[]"
                                                                            id="<?php echo $tour ?>"
                                                                            value=" <?php echo $tour?>">
                                                                        <label class="form-check-label"
                                                                            for="flexCheckDefault">
                                                                            <?php echo $tour ?>
                                                                        </label>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="newShowLabel">Add a new tour</h6>

                                                            <div>
                                                                <label>Tour Name</label>

                                                                <input type="text" class="form-control" id="newTourName"
                                                                    name="newTourName" placeholder="Tour Name">
                                                            </div>
                                                            <div>
                                                                <label>Start Date</label>
                                                                <input type="date" class="form-control"
                                                                    id="tourStartDate" name="tourStartDate"
                                                                    placeholder="01-JAN-2000">
                                                            </div>
                                                            <div>
                                                                <label>End Date</label>
                                                                <input type="date" class="form-control" id="tourEndDate"
                                                                    name="tourEndDate" placeholder="End Date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">


                                                        <h5 for="showNameLabel">Add Shows</h5>

                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="showsSelection">Select from existing
                                                                shows</h6>

                                                            <div class="d-flex flex-row">

                                                                <div
                                                                    style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                    <?php foreach($shows as $show): ?>
                                                                    <?php $show ?>
                                                                    <div class="m-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="existingShows[]"
                                                                            id="<?php echo $show ?>"
                                                                            value=" <?php echo $show?>">
                                                                        <label class="form-check-label"
                                                                            for="flexCheckDefault">
                                                                            <?php echo $show ?>
                                                                        </label>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="newShowLabel">Add a new
                                                                show</h6>

                                                            <div>
                                                                <label>Venue</label>
                                                                <input type="text" class="form-control" id="showVenue"
                                                                    name="showVenue" placeholder="Show Venue">
                                                            </div>
                                                            <small id="showCreationHelp"
                                                                class="form-text text-muted">Venue is required for show
                                                                creation</small>
                                                            <div>
                                                                <label>Show Date</label>
                                                                <input type="date" class="form-control" id="showDate"
                                                                    name="showDate" placeholder="01-JAN-2000">
                                                            </div>
                                                            <small id="showCreationHelp"
                                                                class="form-text text-muted">Show Date is required for
                                                                show creation</small>
                                                            <div>
                                                                <label>Tickets Sold</label>
                                                                <input type="number" class="form-control"
                                                                    id="ticketsSold" name="ticketsSold"
                                                                    placeholder="Tickets Sold">
                                                            </div>
                                                            <div>
                                                                <label>Cost Per Ticket</label>
                                                                <input type="number" class="form-control"
                                                                    id="costperTicket" name="costperTicket"
                                                                    placeholder="Cost">

                                                            </div>
                                                            <div>
                                                                <label>Revenue</label>
                                                                <input type="number" class="form-control" id="revenue"
                                                                    name="revenue" placeholder="Revenue">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <hr>
                                                    <h5 for="TourNameLabel">Add Albums</h5>
                                                    <h6 class="mt-3">Select from existing albums</h6>
                                                    <div
                                                        style="height: 150px; white-space: nowrap; overflow-y: scroll;">

                                                        <?php foreach($albums as $album): ?>
                                                        <div class="m-1">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="existingAlbumSelect[]" id="<?php echo $album ?>"
                                                                value=" <?php echo $album?>">
                                                            <label class="form-check-label" for="flexCheckDefault">
                                                                <?php echo $album ?>
                                                            </label>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <h6 class="mt-3" for="newAlbumNamesLabel">Add a new
                                                            album</h6>

                                                        <div class="row">
                                                            <div class="col">
                                                                <label>Album ID</label>
                                                                <input type="text" class="form-control" id="newAlbumId"
                                                                    name="newAlbumId" placeholder="Album Id">
                                                            </div>
                                                            <small id="albumCreationHelp"
                                                                class="form-text text-muted">Album ID is required for
                                                                album creation</small>
                                                            <div class="col">
                                                                <label>Title</label>
                                                                <input type="text" class="form-control" id="title"
                                                                    name="title" placeholder="Album Title">
                                                            </div>
                                                            <small id="groupCreationHelp"
                                                                class="form-text text-muted">Title is required for album
                                                                creation</small>
                                                            <div class="col">
                                                                <label>Genre</label>
                                                                <input type="text" class="form-control" id="genre"
                                                                    name="genre" placeholder="Album Genre">
                                                            </div>

                                                            <div class="col">
                                                                <label>Release
                                                                    Date</label>
                                                                <input type="date" class="form-control" id="releaseDate"
                                                                    name="releaseDate" placeholder="01-JAN-2000">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col">
                                                                <label>Price</label>
                                                                <input type="number" class="form-control" id="price"
                                                                    name="price" placeholder="Album Price">
                                                            </div>
                                                            <div class="col">
                                                                <label>Number
                                                                    Sold</label>
                                                                <input type="number" class="form-control"
                                                                    id="numberSold" name="numberSold"
                                                                    placeholder="Number Sold">
                                                            </div>
                                                            <div class="col">
                                                                <label>Expected
                                                                    Sales</label>
                                                                <input type="number" class="form-control"
                                                                    id="expectedSales" name="expectedSales"
                                                                    placeholder="Expected Sales">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="input-group mt-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Add songs</span>
                                                                </div>
                                                                <textarea class="form-control"
                                                                    aria-label="With textarea" id="songlist"
                                                                    name="songlist"
                                                                    placeholder="[songTitle#1, TracklistPosition#1, Duration#1], [songTitle#2, TracklistPosition#2, Duration#2]..."></textarea>
                                                            </div>
                                                            <small id="groupCreationHelp"
                                                                class="form-text text-muted">Please enter values in
                                                                format:
                                                                [songTitle#1, TracklistPosition#1, Duration#1],
                                                                [songTitle#2, TracklistPosition#2, Duration#2]...
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="addNewGroup" class="btn btn-primary">Create
                                                Group</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>




                        </form>
                    </div>

                </div>
                </div>
                </div>
                </div>
                </div>
                <?php foreach ($bandsAndArtists as $bandname => $artists): ?>
                <!--?php
                $re = '/\\[(.*?)\\]/';
                $str = "[s1,d1],[s2,d2]";
                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                $result = [];
                
                foreach ($matches as $match) {
                    $result[] = $match[0];
                }
                $songArray = [];
                foreach($result as $r) {
                    $r = str_replace(array( '[', ']' ), '', $r);
                    $songArr = explode(",", $r);
                    array_push($songArray, $songArr);               
                }
                foreach($songArray as $song){
print_r($song[0]);
                }
            
                        ?-->
                <div class="container card card-body mb-2">
                    <div class="row">
                        <div class="col">
                            <?php echo $bandname ?>
                        </div>
                        <div class="col">
                            <a class="btn btn-primary float-end" data-bs-toggle="collapse"
                                href="#<?php echo str_replace(' ', '', $bandname) ?>" role="button"
                                aria-expanded="false" aria-controls=<?php echo str_replace(' ', '', $bandname) ?>>
                                Details
                            </a>
                        </div>
                    </div>
                    <div class="collapse" id=<?php echo str_replace(' ', '', $bandname) ?>>

                        <table class="table" style="height:10px">
                            <thead>
                                <th scope="col">Artists</th>
                                <th scope="col">Albums/Songs</th>
                                <th scope="col">Shows</th>
                                <th scope="col">Tours</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <th scope="col" style="overflow-x:auto;">
                                        <div>
                                            <?php foreach ($artists as $artist): ?>
                                            <!--?php
                                                    print_r($artist["ARTISTNAME"]);
                                                    debug_to_console($artist["ARTISTNAME"]);

                                                        ?-->
                                            <div class="col-md-auto">
                                                <!-- Button trigger modal -->
                                                <?php 
                                                    $artist_html_tag_id = str_replace(' ', '', $bandname) . str_replace(' ', '', $artist["ARTISTNAME"]);
                                                    
                                                    ?>
                                                <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                                    data-bs-target="#artistinfo<?php echo $artist_html_tag_id ?>">
                                                    <?php echo $artist["ARTISTNAME"]
                                                        ?>
                                                </button>
                                                <!-- Modal -->

                                                <div class="modal fade" id="artistinfo<?php echo $artist_html_tag_id ?>"
                                                    tabindex="-1"
                                                    aria-labelledby="artistinfo<?php echo $artist_html_tag_id ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="artistinfo<?php echo $artist_html_tag_id ?>">
                                                                    Artist Details
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul style="list-style-type:none;">
                                                                    <li>
                                                                        Artist Name:
                                                                        <?php echo $artist["ARTISTNAME"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Date of Birth:
                                                                        <?php echo $artist["DATEOFBIRTH"] ?>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="row d-flex justify-content-end">
                                                                    <div class="col-md-auto">
                                                                        <!-- Button trigger modal -->

                                                                        <button type="button" class="btn btn-warning"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#update<?php echo str_replace(' ', '', $artist["ARTISTNAME"] . $bandname)?>">
                                                                            Update
                                                                        </button>
                                                                        <!-- Modal -->

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-dismiss="modal" data-toggle="modal" class="modal fade"
                                                    id="update<?php echo str_replace(' ', '', $artist["ARTISTNAME"] . $bandname)?>"
                                                    tabindex="-1"
                                                    aria-labelledby="update<?php echo str_replace(' ', '', $artist["ARTISTNAME"] . $bandname)?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="update<?php echo str_replace(' ', '', $artist["ARTISTNAME"] . $bandname)?>">
                                                                    Update Artist
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="groups.php">
                                                                    <input type="hidden" id="updateArtistQueryrequest"
                                                                        name="updateArtistQueryrequest">
                                                                    <div class="form-group">
                                                                        <label for="updateArtistName">Artist
                                                                            Name</label>
                                                                        <input type="text" readonly
                                                                            class="form-control-plaintext"
                                                                            id="updateArtistName"
                                                                            name="updateArtistName"
                                                                            aria-describedby="updateArtistName"
                                                                            value="<?php echo $artist["ARTISTNAME"]; ?>"
                                                                            placeholder="<?php echo $artist["ARTISTNAME"]?>">

                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="artistDOB">Date of Birth</label>
                                                                        <input type="date" class="form-control"
                                                                            id="artistDOB" name="artistDOB"
                                                                            placeholder="<?php echo $artist["DATEOFBIRTH"]?>">
                                                                    </div>
                                                                    <button type="submit" name="updateArtist"
                                                                        class="btn btn-primary">Update</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php endforeach;?>
                                        </div>
                                    </th>


                                    <th scope="col">
                                        <?php foreach ($bandsAndAlbum[$bandname] as $album): ?>
                                        <!--?php
                                                 print_r($album["ALBUMID"]);
                                                 foreach($albumSongs[$album["ALBUMID"]] as $songs) {
                                                    print_r($songs["TITLE"]);
                                                 }
                                                 ?-->
                                        <div>
                                            <div class="col-md-auto">
                                                <!-- Button trigger modal -->
                                                <?php 
                                                $albumid = str_replace(' ', '', $album["ALBUMID"] . $bandname)
                                                ?>
                                                <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                                    data-bs-target="#albuminfo<?php echo $albumid?>">
                                                    <?php echo $album["ALBUMID"] ?>
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="albuminfo<?php echo $albumid?>"
                                                    tabindex="-1" aria-labelledby="albuminfo<?php echo $albumid ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="albuminfo<?php echo $albumid ?>">
                                                                    Album Details
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul style="list-style-type:none;">
                                                                    <li>
                                                                        Album ID:
                                                                        <?php echo $album["ALBUMID"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Title:
                                                                        <?php echo $album["TITLE"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Genre:
                                                                        <?php echo $album["GENRE"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Release Date:
                                                                        <?php echo $album["RELEASEDATE"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Price:
                                                                        <?php echo $album["PRICE"] ?> USD
                                                                    </li>
                                                                    <li>
                                                                        Label Name:
                                                                        <?php echo $album["LABELNAME"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Number Sold:
                                                                        <?php echo $album["NUMBERSOLD"] ?>
                                                                    </li>
                                                                    <li>
                                                                        Expected Sales:
                                                                        <?php echo $album["EXPECTEDSALES"] ?>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div>
                                                                    <div class="row d-flex justify-content-end">
                                                                        <div class="col-md-auto">
                                                                            <!-- Button trigger modal -->
                                                                            <a href="labels.php">
                                                                            <input class="btn btn-light" type="submit" value="Link to Label"/>
                                                                            </a>
                                                                            </form>
                                                                            <a href="contracts.php">
                                                                            <input class="btn btn-light" type="submit" value="Link to Contract"/>
                                                                            </a>
                                                                            </form>
                                                                            <button type="button"
                                                                                class="btn btn-success"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#songs<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                                                Songs
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-warning"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#update<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                                                Update
                                                                            </button>

                                                                            <!-- Modal -->

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-dismiss="modal" data-toggle="modal" class="modal fade"
                                                    id="songs<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>"
                                                    tabindex="-1"
                                                    aria-labelledby="songs<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="songs<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                                    Songs
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul style="list-style-type:none;">
                                                                    <?php foreach ($albumSongs[$album["ALBUMID"]] as $song): ?>
                                                                    <div>
                                                                        <li>
                                                                            Title:
                                                                            <?php echo $song["TITLE"] ?>
                                                                            ,

                                                                            Tracklist Position:
                                                                            <?php echo $song["TRACKLISTPOSITION"] ?>
                                                                            ,
                                                                            Duration:
                                                                            <?php echo $song["DURATION"] ?>

                                                                        </li>
                                                                    </div>
                                                                    <?php endforeach;?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-dismiss="modal" data-toggle="modal" class="modal fade"
                                                    id="update<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>"
                                                    tabindex="-1"
                                                    aria-labelledby="update<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="update<?php echo str_replace(' ', '', $album["ALBUMID"] . $bandname)?>">
                                                                    Update Album
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST" action="groups.php">
                                                                    <input type="hidden" id="updateAlbumQueryrequest"
                                                                        name="updateAlbumQueryrequest">
                                                                    <div class="form-group">
                                                                        <label for="updateArtistName">Artist
                                                                            Name</label>
                                                                        <input type="text" readonly
                                                                            class="form-control-plaintext"
                                                                            id="updateAlbumID" name="updateAlbumID"
                                                                            aria-describedby="updateAlbumID"
                                                                            value="<?php echo $album["ALBUMID"]; ?>"
                                                                            placeholder="<?php echo $album["ALBUMID"]?>">

                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="updateTitle">Title</label>
                                                                        <input type="text" class="form-control"
                                                                            id="updateTitle" name="updateTitle"
                                                                            placeholder="<?php echo $album["TITLE"]?>">
                                                                        <label for="updateGenre">Genre</label>
                                                                        <input type="text" readonly
                                                                            class="form-control-plaintext"
                                                                            id="updateGenre" name="updateGenre"
                                                                            placeholder="<?php echo $album["GENRE"]?>">
                                                                        <label for="updateReleaseDate">Release
                                                                            Date</label>
                                                                        <input type="date" class="form-control"
                                                                            id="updateReleaseDate"
                                                                            name="updateReleaseDate"
                                                                            placeholder="<?php echo $album["RELEASEDATE"]?>">

                                                                        <label for="updateNumberSold">Number
                                                                            Sold</label>
                                                                        <input type="number" class="form-control"
                                                                            id="updateNumberSold"
                                                                            name="updateNumberSold"
                                                                            placeholder="<?php echo $album["NUMBERSOLD"]?>">
                                                                        <label for="updateExpectedSales">Expected
                                                                            Sales</label>
                                                                        <input type="text" readonly
                                                                            class="form-control-plaintext"
                                                                            id="updateExpectedSales"
                                                                            name="updateExpectedSales"
                                                                            placeholder="<?php echo $album["EXPECTEDSALES"]?>">
                                                                        <label for="updateLabelName">Label Name</label>
                                                                        <input type="text" readonly
                                                                            class="form-control-plaintext"
                                                                            id="updateLabelName" name="updateLabelName"
                                                                            placeholder="<?php echo $album["LABELNAME"]?>">
                                                                    </div>
                                                                    <button type="submit" name="updateAlbum"
                                                                        class="btn btn-primary">Update</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach;?>
                                    </th>

                                    <th scope="row">
                                        <?php foreach ($bandsAndShows[$bandname] as $show): ?>
                                        <div>
                                            <div>
                                                <div class="col-md-auto">
                                                    <!-- Button trigger modal -->
                                                    <?php $showid = str_replace(' ', '', $show["VENUE"] . $show["SHOWDATE"] . $bandname)?>
                                                    <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                                        data-bs-target="#showinfo<?php echo $showid ?>">
                                                        <?php echo $show["VENUE"] ?>/
                                                        <?php echo $show["SHOWDATE"] ?>
                                                    </button>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="showinfo<?php echo $showid ?>"
                                                        tabindex="-1" aria-labelledby="showinfo<?php $showid ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="showinfo<?php echo $showid ?>">
                                                                        Show Details
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <ul style="list-style-type:none;">
                                                                        <li>
                                                                            Venue:
                                                                            <?php echo $show["VENUE"] ?>
                                                                        </li>
                                                                        <li>
                                                                            Show Date:
                                                                            <?php echo $show["SHOWDATE"] ?>
                                                                        </li>
                                                                        <li>
                                                                            Tikets Sold:
                                                                            <?php echo $show["TICKETSSOLD"] ?>
                                                                        </li>
                                                                        <li>
                                                                            Cost Per Ticket:
                                                                            <?php echo $show["COSTPERTICKET"] ?>
                                                                        </li>
                                                                        <li>
                                                                            Revenue:
                                                                            <?php echo $show["REVENUE"] ?>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach;?>
                                    </th>


                                    <th scope="col">
                                        <?php foreach ($bandsAndTour[$bandname] as $tour): ?>
                                        <div>
                                            <?php $tourid = str_replace(' ', '', $bandname) . str_replace(' ', '', $tour["TOURNAME"]) . str_replace(' ', '', $tour["STARTDATE"]) ?>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                                data-bs-target="#tourinfo<?php echo $tourid ?>">
                                                <?php echo $tour["TOURNAME"] ?>/
                                                <?php echo $tour["STARTDATE"] ?>
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="tourinfo<?php echo $tourid ?>" tabindex="-1"
                                                aria-labelledby="tourinfo<?php $tourid ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="tourinfo<?php  $tourid ?>">
                                                                Tour Details
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul style="list-style-type:none;">
                                                                <li>
                                                                    Tour Name:
                                                                    <?php echo $tour["TOURNAME"] ?>
                                                                </li>
                                                                <li>
                                                                    Start Date:
                                                                    <?php echo $tour["STARTDATE"] ?>
                                                                </li>
                                                                <li>
                                                                    End Date:
                                                                    <?php echo $tour["ENDDATE"] ?>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach;?>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row d-flex justify-content-end">
                            <div class="col-md-auto">
                                <!-- Button trigger modal -->
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#update<?php echo str_replace(' ', '', $bandname) ?>">
                                            Update
                                        </button>
                                    </div>
                                    <div class="col">
                                        <div class="col-md-auto">
                                        <form method="POST" action="groups.php">
                                        <input type="hidden" id="DeleteGroupQueryrequest"
                                                        name="DeleteGroupQueryrequest">
                                                <!--?php $rand=rand(); $_SESSION['rand']=$rand;?-->
                                                <div class="form-group">
                                                <input type="hidden" readonly
                                                                            class="form-control-plaintext"
                                                                            id="deleteBandButton" name="deleteBandButton"
                                                                            placeholder="<?php echo $bandname?>"
                                                                            value="<?php echo $bandname?>">
                                            <button  class="btn btn-danger" data-bs-toggle="collapse"
                                                href="#delete<?php echo str_replace(' ', '', $bandname) ?>"
                                                role="button" aria-expanded="false"
                                                aria-controls="delete<?php echo str_replace(' ', '', $bandname) ?>"
                                                type="submit"
                                                name="deleteBand"
                                                >
                                                Delete
                                            </button >
                                        </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="update<?php echo str_replace(' ', '', $bandname) ?>"
                                    tabindex="-1" aria-labelledby="update<?php echo str_replace(' ', '', $bandname) ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="addGroups">Update Groups</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="groups.php">
                                                    <!--?php $rand=rand(); $_SESSION['rand']=$rand;?-->

                                                    <input type="hidden" id="updateGroupQueryrequest"
                                                        name="updateGroupQueryrequest">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">

                                                                <h6 class="mt-3" for="updateGroupNameLabel">Group Name
                                                                </h6>

                                                                <input type="text" readonly
                                                                    class="form-control-plaintext" id="groupName"
                                                                    name="groupName"
                                                                    value="<?php echo $bandname ?>"
                                                                    placeholder="<?php echo $bandname ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <small id="groupCreationHelp"
                                                                    class="form-text text-muted">You need to assign at
                                                                    least
                                                                    one artist to a group.</small>
                                                                <h6 class="mt-3" for="artistSelection">Select from
                                                                    existing
                                                                    artists</h6>

                                                                <div class="d-flex flex-row">

                                                                    <div
                                                                        style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                        <?php
                                                                        $previouslySelectedArtists = [];

                                                                         foreach($artists as $artist){
                                                                            array_push($previouslySelectedArtists, $artist["ARTISTNAME"]);
                                                                        } ?>
                                                                        <?php foreach($artistsArray as $artist): ?>
                                                                        <div class="m-1">
                                                                            <?php
                                                                                if(in_array($artist, $previouslySelectedArtists)) :?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="existingArtistSelect[]"
                                                                                id="<?php echo $artist ?>"
                                                                                value=" <?php echo $artist?>" disabled>
                                                                            <?php else: ?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="existingArtistSelect[]"
                                                                                id="<?php echo $artist ?>"
                                                                                value=" <?php echo $artist?>">
                                                                            <?php endif ?>
                                                                            <label class="form-check-label"
                                                                                for="flexCheckDefault">
                                                                                <?php echo $artist ?>
                                                                            </label>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <h6 class="mt-3" for="newArtistNamesLabel">Add a new
                                                                    artist</h6>
                                                                <div>
                                                                    <label>Artist Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="newArtistName" name="newArtistName"
                                                                        placeholder="Artist Name">
                                                                </div>
                                                                <div>
                                                                    <label>Date of birth</label>
                                                                    <input type="date" class="form-control" id="DOB"
                                                                        name="DOB" placeholder="01-JAN-2000">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">


                                                            <h5 for="TourNameLabel">Add Tours</h5>

                                                            <div class="form-group">
                                                                <h6 class="mt-3" for="tourSelection">Select from
                                                                    existing
                                                                    tours</h6>

                                                                <div class="d-flex flex-row">

                                                                    <div
                                                                        style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                        <?php
                                                                        $previouslySelectedTours = [];
                                                                        foreach($bandsAndTour[$bandname] as $tour){
                                                                            array_push($previouslySelectedTours, $tour["TOURNAME"] . "/" . $tour["STARTDATE"]);
                                                                        } ?>
                                                                        <?php foreach($tours as $tour): ?>
                                                                        <div class="m-1">

                                                                            <?php if(in_array($tour, $previouslySelectedTours)) :?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="existingTours[]"
                                                                                id="<?php echo $tour ?>"
                                                                                value=" <?php echo $tour?>" disabled>
                                                                            <?php else: ?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="existingTours[]"
                                                                                id="<?php echo $tour ?>"
                                                                                value=" <?php echo $tour?>">
                                                                            <?php endif ?>

                                                                            <label class="form-check-label"
                                                                                for="flexCheckDefault">
                                                                                <?php echo $tour ?>
                                                                            </label>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <h6 class="mt-3" for="newShowLabel">Add a new tour</h6>

                                                                <div>
                                                                    <label>Tour Name</label>

                                                                    <input type="text" class="form-control"
                                                                        id="newTourName" name="newTourName"
                                                                        placeholder="Tour Name">
                                                                </div>
                                                                <div>
                                                                    <label>Start Date</label>
                                                                    <input type="date" class="form-control"
                                                                        id="tourStartDate" name="tourStartDate"
                                                                        placeholder="01-JAN-2000">
                                                                </div>
                                                                <div>
                                                                    <label>End Date</label>
                                                                    <input type="date" class="form-control"
                                                                        id="tourEndDate" name="tourEndDate"
                                                                        placeholder="End Date">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">


                                                            <h5 for="showNameLabel">Add Shows</h5>

                                                            <div class="form-group">
                                                                <h6 class="mt-3" for="showsSelection">Select from
                                                                    existing
                                                                    shows</h6>

                                                                <div class="d-flex flex-row">

                                                                    <div
                                                                        style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                                        <?php
                                                                        $previouslySelectedShows = [];
                                                                        foreach($bandsAndShows[$bandname] as $show){
                                                                            array_push($previouslySelectedShows, $show["VENUE"] . "/" . $show["SHOWDATE"]);
                                                                        } ?>
                                                                        <?php foreach($shows as $show): ?>
                                                                        <div class="m-1">
                                                                            <?php if(in_array($show, $previouslySelectedShows)) :?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="existingShows[]"
                                                                                id="<?php echo $show ?>"
                                                                                value=" <?php echo $show?>" disabled>
                                                                            <?php else: ?>
                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="existingShows[]"
                                                                                id="<?php echo $show ?>"
                                                                                value=" <?php echo $show?>">
                                                                            <?php endif ?>
                                                                            <label class="form-check-label"
                                                                                for="flexCheckDefault">
                                                                                <?php echo $show ?>
                                                                            </label>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <h6 class="mt-3" for="newShowLabel">Add a new
                                                                    show</h6>

                                                                <div>
                                                                    <label>Venue</label>
                                                                    <input type="text" class="form-control"
                                                                        id="showVenue" name="showVenue"
                                                                        placeholder="Show Venue">
                                                                </div>
                                                                <small id="showCreationHelp"
                                                                    class="form-text text-muted">Venue is required for
                                                                    show
                                                                    creation</small>
                                                                <div>
                                                                    <label>Show Date</label>
                                                                    <input type="date" class="form-control"
                                                                        id="showDate" name="showDate"
                                                                        placeholder="01-JAN-2000">
                                                                </div>
                                                                <small id="showCreationHelp"
                                                                    class="form-text text-muted">Show Date is required
                                                                    for
                                                                    show creation</small>
                                                                <div>
                                                                    <label>Tickets Sold</label>
                                                                    <input type="number" class="form-control"
                                                                        id="ticketsSold" name="ticketsSold"
                                                                        placeholder="Tickets Sold">
                                                                </div>
                                                                <div>
                                                                    <label>Cost Per Ticket</label>
                                                                    <input type="number" class="form-control"
                                                                        id="costperTicket" name="costperTicket"
                                                                        placeholder="Cost">

                                                                </div>
                                                                <div>
                                                                    <label>Revenue</label>
                                                                    <input type="number" class="form-control"
                                                                        id="revenue" name="revenue"
                                                                        placeholder="Revenue">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="row">
                                                        <hr>
                                                        <h5 for="TourNameLabel">Add Albums</h5>
                                                        <h6 class="mt-3">Select from existing albums</h6>
                                                        <div
                                                            style="height: 150px; white-space: nowrap; overflow-y: scroll;">
                                                            <?php
                                                                        $previouslySelectedAlbums = [];
                                                                        foreach($bandsAndAlbum[$bandname] as $album){
                                                                            array_push($previouslySelectedAlbums, $album["ALBUMID"]);
                                                                        } ?>

                                                            <?php foreach($albums as $album): ?>
                                                            <div class="m-1">
                                                                <?php if(in_array($album, $previouslySelectedAlbums)) :?>
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="existingAlbumSelect[]"
                                                                    id="<?php echo $album ?>"
                                                                    value=" <?php echo $album?>" disabled>
                                                                <?php else: ?>
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="existingAlbumSelect[]"
                                                                    id="<?php echo $album ?>"
                                                                    value=" <?php echo $album?>">
                                                                <?php endif ?>

                                                                <label class="form-check-label" for="flexCheckDefault">
                                                                    <?php echo $album ?>
                                                                </label>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <h6 class="mt-3" for="newAlbumNamesLabel">Add a new
                                                                album</h6>

                                                            <div class="row">
                                                                <div class="col">
                                                                    <label>Album ID</label>
                                                                    <input type="text" class="form-control"
                                                                        id="newAlbumId" name="newAlbumId"
                                                                        placeholder="Album Id">
                                                                </div>
                                                                <small id="albumCreationHelp"
                                                                    class="form-text text-muted">Album ID is required
                                                                    for
                                                                    album creation</small>
                                                                <div class="col">
                                                                    <label>Title</label>
                                                                    <input type="text" class="form-control" id="title"
                                                                        name="title" placeholder="Album Title">
                                                                </div>
                                                                <small id="groupCreationHelp"
                                                                    class="form-text text-muted">Title is required for
                                                                    album
                                                                    creation</small>
                                                                <div class="col">
                                                                    <label>Genre</label>
                                                                    <input type="text" class="form-control" id="genre"
                                                                        name="genre" placeholder="Album Genre">
                                                                </div>

                                                                <div class="col">
                                                                    <label>Release
                                                                        Date</label>
                                                                    <input type="date" class="form-control"
                                                                        id="releaseDate" name="releaseDate"
                                                                        placeholder="01-JAN-2000">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <label>Price</label>
                                                                    <input type="number" class="form-control" id="price"
                                                                        name="price" placeholder="Album Price">
                                                                </div>
                                                                <div class="col">
                                                                    <label>Number
                                                                        Sold</label>
                                                                    <input type="number" class="form-control"
                                                                        id="numberSold" name="numberSold"
                                                                        placeholder="Number Sold">
                                                                </div>
                                                                <div class="col">
                                                                    <label>Expected
                                                                        Sales</label>
                                                                    <input type="number" class="form-control"
                                                                        id="expectedSales" name="expectedSales"
                                                                        placeholder="Expected Sales">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="input-group mt-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Add songs</span>
                                                                    </div>
                                                                    <textarea class="form-control"
                                                                        aria-label="With textarea" id="songlist"
                                                                        name="songlist"
                                                                        placeholder="[songTitle#1, TracklistPosition#1, Duration#1], [songTitle#2, TracklistPosition#2, Duration#2]..."></textarea>
                                                                </div>
                                                                <small id="songCreationHelp"
                                                                    class="form-text text-muted">Please enter values in
                                                                    format:
                                                                    [songTitle#1, TracklistPosition#1, Duration#1],
                                                                    [songTitle#2, TracklistPosition#2, Duration#2]...
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary"
                                                    name="saveUpdateGroup">Save
                                                    changes</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
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

                </div>
                </div>
                </div>
                </div>
                <?php endforeach;?>
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
    <?php
$success = true; //keep track of errors so it redirects the page only if there are no errors
$db_conn = null; // edit the login credentials in connectToDB()
$show_debug_alert_messages = false; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message)
{
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type=' text/javascript'>alert('" . $message . "');</script>";
    }
}

function printResult($result)
{ //prints results from a select statement
    echo "<br>Retrieved data from table demoTable:<br>";
    echo "<table>";
    echo "<tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                </tr>";

    while ($row = oci_fetch_array($result, OCI_BOTH)) {
        echo "<tr>
                                    <td>" . $row["ID"] . "</td>
                                    <td>" . $row["NAME"] . "</td>
                                </tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function handleResetRequest()
{
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE demoTable");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
    oci_commit($db_conn);
}

function handleInsertRequest()
{
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array(
        ":bind1" => $_POST['insNo'],
        ":bind2" => $_POST['insName'],
    );

    $alltuples = array(
        $tuple,
    );

    executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
    oci_commit($db_conn);
}

function handleCountRequest()
{
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM demoTable");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
    }
}

function getNestedAggVal() {
    global $db_conn;
    if (connectToDB()) {
        $result = executePlainSQL("select a.title, a.genre from album a where a.numbersold > ALL (select avg(b.expectedsales) from album b group by title, genre)");
        $titleGenreArray = [];
        while ($row = oci_fetch_assoc($result)) {
            array_push($titleGenreArray, $row);
        }
    }
    OCICommit($db_conn);
    return $titleGenreArray;
}

function getListOfBandOnUserCondition() {
    global $db_conn;
    $salesCondition = trim($_POST['salesCondition']);
    if (connectToDB()) {
        $result = executePlainSQL("select bandname from contributesto natural join album where EXPECTEDSALES >= '{$salesCondition}'");
        $bandArray = [];
        while ($row = oci_fetch_assoc($result)) {
            array_push($bandArray, $row["BANDNAME"]);
        }
    }
    ;
    echo "<div justify-content-evenly align-items-start>";
    echo '<div class="card fixed-top" style="width: 18rem;">';
    echo '<div class="card-body">';
    echo  '<h5 class="card-title">Top Brands</h5>';
    if(count($bandArray) ==0) {
        echo  '<p class="card-text">' . none . '</p>';
    }
    foreach($bandArray as $band):
    echo  '<p class="card-text">' . $band . '</p>';
    endforeach;
    echo '<a href="groups.php" class="btn btn-primary">Close</a>' ;
    echo '</div>
    </div>
    </div>';
    OCICommit($db_conn);
    return $bandArray;
}

function handleUserInputQuery() {
    global $db_conn, $success;
    $selectAttribute = trim($_POST['selectAttribute']);
    $selectTable = $_POST['selectTable'];
    $equalCondition = trim($_POST['equalCondition']);
    $condition1Value = $_POST['condition1Value'];
    $compareCondition = trim($_POST['compareCondition']);
    $condition2Value = $_POST['condition2Value'];

    if (connectToDB()) {
        $result = executePlainSQL("Select $selectAttribute from  $selectTable where $equalCondition = '$condition1Value' and $compareCondition = '$condition2Value'");
        $resultArray = [];
        $selectAttribute = strtoupper($selectAttribute);
        if($success) {
            while ($row = oci_fetch_assoc($result)) {
            array_push($resultArray,$row["$selectAttribute"]);
            }
        echo "<div justify-content-evenly align-items-start>";
        echo '<div class="card fixed-top" style="width: 18rem;">';
        echo '<div class="card-body">';
        echo  '<h5 class="card-title">User Selection</h5>';
        if(count($resultArray) ==0) {
            echo  '<p class="card-text">' . none . '</p>';
        }
        foreach($resultArray as $user):
        echo  '<p class="card-text">' . $user . '</p>';
        endforeach;
        echo '<a href="groups.php" class="btn btn-primary">Close</a>' ;
        echo '</div>
        </div>
        </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">
           incorrect input values
       </div>';
    }
    }
    OCICommit($db_conn);
    
}

function handleAddNewGroup() {
    global $db_conn;
        //Getting the values from user and insert data into the table
        if (connectToDB()) {
            $bandname = trim($_POST['groupName']);
            $DOB = $_POST['DOB'];
            $newArtistName = trim($_POST['newArtistName']);
            $existingArtistArray = $_POST['existingArtistSelect'];
            if($DOB) {
                $date=date_create($DOB);
                $date = date_format($date,"d-M-Y");
            }           
            
            $bandExists = executePlainSQL("select * from band where bandname = '$bandname'");

            if(oci_fetch_assoc($bandExists)) {
              echo
              '<div class="alert alert-danger" role="alert">
                    Band exists! Please choose another name.
                </div>';
            } else if(!$newArtistName && sizeof($existingArtistArray) ==0) {
                echo '<div class="alert alert-danger" role="alert">
                   A band cannot exist without an artist. Please choose an existing artist or create a new one.
                </div>';
            } else {
                executePlainSQL("insert into band values ('$bandname')");
                echo                
                '<div class="alert alert-success" role="alert">
                    Band was created!
                  </div>';
                if($newArtistName) {
                    executePlainSQL("insert into artist values ('$newArtistName', '$date')");
                    executePlainSQL("insert into memberof values ('$newArtistName', '$bandname')");
                }
                foreach($existingArtistArray as $artist) {
                    $artist = trim($artist);
                    executePlainSQL("insert into memberof values ('$artist', '$bandname')");
                }
                handleAddToursToGroup($bandname);
            handleAddShowsToGroup($bandname);
            handleAddAlbumsToGroup($bandname);
                
            }
            
            OCICommit($db_conn);
            }
        
}

function handleAddToursToGroup($bandname) {
    global $db_conn;
        //Getting the values from user and insert data into the table
        if (connectToDB()) {
            $newTourName = trim($_POST['newTourName']);
            $existingToursArray = $_POST['existingTours'];
            $tourEndDate = trim($_POST['tourEndDate']);
            $tourStartDate = $_POST['tourStartDate'];

            if($tourStartDate) {
                $startDate=date_create($tourStartDate);
                $startDate = date_format($startDate,"d-M-Y");
            }
            if($tourEndDate) {
                $tourEndDate=date_create($tourEndDate);
                $tourEndDate = date_format($tourEndDate,"d-M-Y");
            }
            
            
           $tourExists = executePlainSQL("select * from tour where tourname = '$newTourName' and startdate='$startDate'");
            

            if(oci_fetch_assoc($tourExists)) {
              echo
              '<div class="alert alert-danger" role="alert">
                    Tour exists! Please choose another name or start date.
                </div>';
                
            } else if($newTourName && (!$startDate || !$tourEndDate)){
                echo                
                '<div class="alert alert-danger" role="alert">
                    Tour creation failed.
                  </div>';
            }
            else {
                if($newTourName && $startDate && $tourEndDate) {
                executePlainSQL("insert into tour values ('$newTourName', '$startDate', '$tourEndDate' )");
                
                executePlainSQL("insert into goeson values ('$bandname', '$newTourName', '$startDate' )");
                }
                if(sizeof($existingToursArray) != 0) {
                    foreach($existingToursArray as $tour) {
                        $tName = trim(explode("/", $tour)[0]);
                        $tStartDate = trim(explode("/", $tour)[1]);

                      executePlainSQL("insert into goeson values ('$bandname', '$tName', '$tStartDate' )");
                    }
                }

            }
            OCICommit($db_conn);
        }
    }

function handleAddShowsToGroup($bandname) {
    global $db_conn;
        //Getting the values from user and insert data into the table
    if (connectToDB()) {
        $showVenue = trim($_POST['showVenue']);
        $existingShowsArray = $_POST['existingShows'];
        $showDate = trim($_POST['showDate']);
        $ticketsSold = $_POST['ticketsSold'];
        $costperTicket = $_POST['costperTicket'];
        $revenue = $_POST['revenue'];
        if($showDate) {
            $showDate=date_create($showDate);
            $showDate = date_format($showDate,"d-M-Y");
        }
        
        
       $showExists = executePlainSQL("select * from musicshow where venue = '$showVenue' and showdate='$showDate'");
        
        if(oci_fetch_assoc($showExists)) {
          echo
          '<div class="alert alert-danger" role="alert">
                Show exists! Please choose another venue or show date.
            </div>';
        } else if(($showVenue && !$showDate) || (!$showVenue && $showDate)){
            echo                
            '<div class="alert alert-danger" role="alert">
                Show creation failed.
              </div>';
        }
        else {
            if($showVenue && $showDate) {
                if($ticketsSold && $costperTicket) {
            executePlainSQL("insert into showrevenuelookup values ('$ticketsSold', '$costperTicket', '$revenue')");
            }
            executePlainSQL("insert into musicshow values ('$showVenue', '$showDate', '$ticketsSold', '$costperTicket' )");
            
            executePlainSQL("insert into performs values ('$bandname', '$showVenue', '$showDate' )");
            }
            if(sizeof($existingShowsArray) != 0) {
                foreach($existingShowsArray as $tour) {
                    $sVenue = trim(explode("/", $tour)[0]);
                    $sShowdate = trim(explode("/", $tour)[1]);
                  executePlainSQL("insert into performs values ('$bandname', '$sVenue', '$sShowdate' )");
                }
            }
        }
        OCICommit($db_conn);
    }
}
    

function getValuesFromText($str) {
    $re = '/\\[(.*?)\\]/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $result = [];
        
        foreach ($matches as $match) {
            $result[] = $match[0];
        }
        $songArray = [];
        foreach($result as $r) {
            $r = str_replace(array( '[', ']' ), '', $r);
            $songArr = explode(",", $r);
            array_push($songArray, $songArr);               
        }
        return $songArray;
}        

function handleAddAlbumsToGroup($bandname) {
    global $db_conn;
        //Getting the values from user and insert data into the table
        if (connectToDB()) {
            $newAlbumId = trim($_POST['newAlbumId']);
            $existingAlbumSelect = $_POST['existingAlbumSelect'];
            $price = trim($_POST['price']);
            $releaseDate = $_POST['releaseDate'];
            $genre = $_POST['genre'];
            $title = $_POST['title'];
            $numberSold = $_POST['numberSold'];
            $expectedSales = $_POST['expectedSales'];
            $songlist = $_POST['songlist'];
            
            $songArray = getValuesFromText($songlist);
            if($releaseDate) {
                $releaseDate=date_create($releaseDate);
                $releaseDate = date_format($releaseDate,"d-M-Y");
            }                    
            
           $albumExists = executePlainSQL("select * from album where albumid = '$newAlbumId'");
                   
            if(oci_fetch_assoc($albumExists)) {
              echo
              '<div class="alert alert-danger" role="alert">
                    Album exists! Please choose another album id.
                </div>';
            } else if(($newAlbumId && !$title) || (!$newAlbumId && $title)){
                echo                
                '<div class="alert alert-danger" role="alert">
                    Album creation failed.
                  </div>';
            }else {
                if($genre && $expectedSales && $price) {
                    executePlainSQL("insert into revenuelookup values ('$genre', '$expectedSales', '$price')");
                }
                if($newAlbumId && $title) {
                    executePlainSQL("insert into album values ('$newAlbumId', null, null, '$title', '$releaseDate', '$genre', '$numberSold', '$expectedSales')");
                    executePlainSQL("insert into contributesto values ('$newAlbumId', '$bandname')");
                    if($songArray) {
                        foreach($songArray as $song) {
                            if($song[2]) {
                                $duration = (float)($song[2]);
                            }
                            if($song[1]) {
                                $tracklistPosition = (float)($song[1]);
                            }
                            executePlainSQL("insert into song values ('$song[0]', '$newAlbumId', '$tracklistPosition', $duration)");
                        }
                    }
                }
                if(sizeof($existingAlbumSelect) != 0) {
                    foreach($existingAlbumSelect as $album) {
                        $album = trim($album);
                    executePlainSQL("insert into contributesto values ('$album', '$bandname')");
                    }
                }
            }
        }
        OCICommit($db_conn);
    }
        
function handleUpdateGroup() {
    global $db_conn;
    //Getting the values from user and insert data into the table
    if (connectToDB()) {
        $bandname = trim($_POST['groupName']);
        $DOB = $_POST['DOB'];
        $newArtistName = trim($_POST['newArtistName']);
        $existingArtistArray = $_POST['existingArtistSelect'];
        if($DOB) {
            $date=date_create($DOB);
            $date = date_format($date,"d-M-Y");
        }
        if($newArtistName) {
            executePlainSQL("insert into artist values ('$newArtistName', '$date')");
            executePlainSQL("insert into memberof values ('$newArtistName', '$bandname')");
        }
        foreach($existingArtistArray as $artist) {
            $artist = trim($artist);
            executePlainSQL("insert into memberof values ('$artist', '$bandname')");
        }
        handleAddToursToGroup($bandname);
        handleAddShowsToGroup($bandname);
        handleAddAlbumsToGroup($bandname);
        
    }
    OCICommit($db_conn);
}
function handleUpdateArtist() {
    global $db_conn;
    if (connectToDB()) {
        $updateArtistName = trim($_POST['updateArtistName']);
        $artistDOB = $_POST['artistDOB'];
        if($artistDOB) {
            $artistDOB=date_create($artistDOB);
            $artistDOB = date_format($artistDOB,"d-M-Y");
        }
        executePlainSQL("update artist set dateofbirth ='$artistDOB' where artistname='$updateArtistName'");
    }
    OCICommit($db_conn);
}
function handleUpdateAlbum() {
    global $db_conn;
    //Getting the values from user and insert data into the table
    if (connectToDB()) {
        $updateAlbumID = trim($_POST['updateAlbumID']);
        $updateTitle = $_POST['updateTitle'];
        $updateGenre = $_POST['updateGenre'];
        $updateReleaseDate = $_POST['updateReleaseDate'];
        $updateNumberSold = $_POST['updateNumberSold'];
        $updateExpectedSales = $_POST['updateExpectedSales'];
        $updateLabelName = $_POST['updateLabelName'];
        if($updateReleaseDate) {
            $updateReleaseDate=date_create($updateReleaseDate);
            $updateReleaseDate = date_format($updateReleaseDate,"d-M-Y");
        }
        if($updateTitle) {
            executePlainSQL("update album set title ='$updateTitle' where albumid='$updateAlbumID'");
        }
        if($updateReleaseDate) {
            executePlainSQL("update album set releasedate ='$updateReleaseDate' where albumid='$updateAlbumID'");
        }
        if($updateNumberSold) {
            $updateNumberSold = (float)$updateNumberSold;
            executePlainSQL("update album set numbersold ='$updateNumberSold' where albumid='$updateAlbumID'");
        }
    }
    OCICommit($db_conn);
}

function handleDeleteGroup()
{
    global $db_conn;
    //Getting the values from user and insert data into the table
    if (connectToDB()) {
        $bandname = $_POST['deleteBandButton'];
        executePlainSQL("delete from band where bandname='$bandname'");
    }
    OCICommit($db_conn);
}            

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly.
// It will make it easier to add/remove functionality.
function handlePOSTRequest()
{
    if (connectToDB()) {
        if (array_key_exists('addNewGroupQueryrequest', $_POST)) {
            handleAddNewGroup();
        } else if (array_key_exists('updateArtistQueryrequest', $_POST)) {
            handleUpdateArtist();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        }
        else if (array_key_exists('updateAlbumQueryrequest', $_POST)) {
            handleUpdateAlbum();
        }
        else if (array_key_exists('updateGroupQueryrequest', $_POST)) {
            handleUpdateGroup();
        }
        else if (array_key_exists('DeleteGroupQueryrequest', $_POST)) {
            handleDeleteGroup();
        }
        else if (array_key_exists('processUserInputQuery', $_POST)) {
            handleUserInputQuery();
        }
        else if (array_key_exists('processUserBandInputQuery', $_POST)) {
            getListOfBandOnUserCondition();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly.
// It will make it easier to add/remove functionality.
function handleGETRequest()
{
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        }

        disconnectFromDB();
    }
}
function debug_to_console($data)
    {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "
                            <script>console.log('Debug Objects: " . $output . "');</script>";
    }

if(isset($_POST['addNewGroup'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
} else if (isset($_POST['updateArtist'])) {
    handleUpdateArtist();
}else if (isset($_POST['updateAlbum'])) {
    handleUpdateAlbum();
}
else if (isset($_POST['saveUpdateGroup'])) {
    handleUpdateGroup();
}
else if (isset($_POST['deleteBand'])) {
    handleDeleteGroup();
}
else if (isset($_POST['processUserInput'])) {
    handleUserInputQuery();
}
else if (isset($_POST['processUserBandInput'])) {
    getListOfBandOnUserCondition();
}
?>
</body>


</html>