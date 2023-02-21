<?php include("includes/init.php");
$title = "DETAILS";
$messages = array();

//duplicate checks
$existing_albums = exec_sql_query($db, "SELECT title FROM albums")->fetchAll(PDO::FETCH_ASSOC);
$album_array = [];
foreach ($existing_albums as $existing_album) {
    array_push($album_array, $existing_album["title"]);
}
$existing_tags = exec_sql_query($db, "SELECT genre FROM tags")->fetchAll(PDO::FETCH_ASSOC);
$tags_array = [];
foreach ($existing_tags as $existing_tag) {
    array_push($tags_array, $existing_tag["genre"]); //add each to array
}
// ------------------------------------------------------------------------------------------------------ //
// get parameter for page display (GET requests)
$get_album = trim($_GET["album"]);
$get_tag = trim($_GET["genre"]);

if(!empty($get_album)) {
    $title = "DETAILS: " . $get_album;
}
elseif(!empty($get_tag)){
    $title = "DETAILS: " . $get_tag;
}
else {
    $title = "DETAILS";
}
// ------------------------------------------------------------------------------------------------------ //
// forms (POST requests)
// Add Album - Delete Album - Tag Album - Untag Album - Create Tag

// ADD ALBUM
$show_title_feedback = FALSE;
$show_artist_feedback = FALSE;
$show_citation_feedback = FALSE;
if (isset($_POST["submit_upload"])) {
  $valid = TRUE;

  //filter input
  $album_title = trim($_POST['title']);
  $artist_name = trim($_POST['artist']);
  $upload= $_FILES['album_art'];
  $citation = trim($_POST['citation']);

  if (empty($album_title) || in_array($album_title, $album_array)) {
    $valid = FALSE;
    $show_title_feedback = TRUE;
  }
  if (empty($artist_name)) {
    $valid = FALSE;
    $show_artist_feedback = TRUE;
  }
  if (empty($citation)) {
    $valid = FALSE;
    $show_citation_feedback = TRUE;
  }

  if($upload['error']==UPLOAD_ERR_FORM_SIZE){
    array_push($messages, "File size exceeds max. Try uploading a smaller photo.");
  }
  if ($valid && $upload['error']==UPLOAD_ERR_OK){ //insert new records
      //extract the file name in order to store it in the database
      $basename = basename($upload["name"]);
      $upload_ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));

      //albums
      $albums_sql = "INSERT INTO albums (artist, title,file_ext, citation) VALUES (:artist, :title,:file_ext, :citation)";
      $albums_params = array(
      ':artist' => $artist_name,
      ':title' => $album_title,
      ':file_ext' => $upload_ext,
      ':citation' => $citation);
      $albums_result = exec_sql_query($db, $albums_sql, $albums_params);

      $alId = $db->lastInsertId("id"); //save for use twice

      //album_tags
      $album_tags_sql = "INSERT INTO album_tags (album_id) VALUES (:album_id)";
      $album_tags_params = array(
      ':album_id' => $alId); //corresponds to album insert
      $album_tags_result = exec_sql_query($db, $album_tags_sql, $album_tags_params);

      //confirmation
      if ($albums_result && $album_tags_result) {
        array_push($messages, "Your music has been added to the playlist!");
      } else {
        array_push($messages, "Your music could not be added. Please try again with different inputs.");
      }

      //store in db
      $new_path = "uploads/albums/" . $alId . "." . $upload_ext;
      move_uploaded_file($_FILES["album_art"]["tmp_name"], $new_path);

    } else {
      array_push($messages, "Failed to add your music due to invalid input. Please make sure the album art is a jpg, jpeg, or png.");
    }
}
// ------------------------------------------------------------------------------------------------------ //
// DELETE ALBUM
$show_toDelete_feedback = FALSE;
if (isset($_POST["deleteAlbum"])) {
    $valid = TRUE;

    //filter input, check against existing albums
    $toDelete = trim($_POST['toDelete']);

    if(empty($toDelete) || !in_array($toDelete, $album_array)){
        $valid = FALSE;
        $show_toDelete_feedback = TRUE;
    }

    if ($valid) { //begin deletion process

        //save album id and file extension
        $info_sql = "SELECT id, file_ext FROM albums WHERE (title = :title)";
        $info_params = array(
            ':title' => $toDelete);
        $info = exec_sql_query($db, $info_sql, $info_params)->fetchAll(PDO::FETCH_ASSOC);

        //albums
        $album_sql = "DELETE FROM albums WHERE (title = :title)";
        $album_params =  array(
            ':title' => $toDelete);
        $album_result = exec_sql_query($db, $album_sql, $album_params);

        //album-tags
        $album_tag_sql = "DELETE FROM album_tags WHERE (album_id = :album_id)";
        $album_tag_params = array(
            ':album_id' => $info[0]["id"]);
        $album_tag_result = exec_sql_query($db, $album_tag_sql, $album_tag_params);

        // unlink photo files
        unlink("uploads/albums/" . $info[0]["id"] . "." . $info[0]["file_ext"]);
        if ($album_result && $album_tag_result) {
            array_push($messages, "The album was successfully deleted.");
        } else {
            array_push($messages, "Failed to delete album. Make sure you didn't forget to select one.");
        }
    } else {
        array_push($messages, "Failed to delete album. Invalid input.");
    }
}
// ------------------------------------------------------------------------------------------------------ //
// ADD TAG
$show_tagToTag_feedback = FALSE; // which tag to add (genre)
$show_addTo_feedback = FALSE; // which album to add it to (title)
if (isset($_POST["addTag"])) {
    $valid = TRUE;

    //filter input
    $addTo = trim($_POST['addTo']); // (title)
    if(empty($addTo) || !in_array($addTo, $album_array)){
        $valid = FALSE;
        $show_addTo_feedback = TRUE;
    }
    //get album id
    $albId_sql = "SELECT id FROM albums WHERE (title = :title)";
    $albId_params = array (
        ":title" => $addTo
    );
    $albId = exec_sql_query($db, $albId_sql, $albId_params)->fetchAll(PDO::FETCH_ASSOC);
    $albId = $albId[0]['id'];

    $tagToTag = trim($_POST['tagToTag']); // (genre)
    if(empty($tagToTag) || !in_array($tagToTag, $tags_array)){
        $valid = FALSE;
        $show_tagToTag_feedback = TRUE;
    }
    //get tag id
    $toTag_sql = "SELECT id FROM tags WHERE (genre = :genre)";
    $toTag_params = array (
        ":genre" => $tagToTag
    );
    $toTag = exec_sql_query($db, $toTag_sql, $toTag_params)->fetchAll(PDO::FETCH_ASSOC);
    $toTag = $toTag[0]['id'];

    //select all of album's tag ids
    $infos_sql = "SELECT tag_id FROM album_tags WHERE (album_id = :album_id)";
    $infos_params = array (
        ":album_id" => $albId
    );
    $infos = exec_sql_query($db, $infos_sql, $infos_params)->fetchAll(PDO::FETCH_ASSOC);
    $info_array = [];
    foreach ($infos as $info){
        // each entry is an array containing ['tag_id']
        array_push($info_array, $info['tag_id']);
    }
    //check if this album already has this tag
    if(in_array($toTag, $info_array)){
        $valid = FALSE;
        array_push($messages, "This album has already been tagged with this genre. Select a different genre or album if you would like.");
    }

    if ($valid) {
        //attach to album
        $addTo_sql = "INSERT INTO album_tags (album_id,tag_id) VALUES (:album_id, :tag_id)";
        $addTo_params = array (
            ":album_id"=>$albId,
            ":tag_id" =>$toTag
        );
        $addTo_result = exec_sql_query($db, $addTo_sql, $addTo_params);

        if ($addTo_result) {
            array_push($messages, "The tag was successfully added to the album.");
        } else {
            array_push($messages, "Could not add tag due to invalid input.");
        }
    }
}
// ------------------------------------------------------------------------------------------------------ //
// UNTAG
$show_removeFrom_feedback = FALSE;
$show_toRemove_feedback = FALSE;
if (isset($_POST["unTag"])){
    $valid = TRUE;

    //filter input
    $removeFrom = trim($_POST['removeFrom']);
    if(empty($removeFrom) || !in_array($removeFrom, $album_array)){
        $valid = FALSE;
        $show_removeFrom_feedback = TRUE;
    }

    $toRemove = trim($_POST['toRemove']);
    if(empty($toRemove) || !in_array($toRemove, $tags_array)){
        $valid = FALSE;
        $show_toRemove_feedback = TRUE;
    }

    //get album id
    $albumId_sql = "SELECT id FROM albums WHERE (title = :title)";
    $albumId_params = array (
        ":title" => $removeFrom
    );
    $albumId = exec_sql_query($db, $albumId_sql, $albumId_params)->fetchAll(PDO::FETCH_ASSOC);
    $albumId = $albumId[0]["id"];

    //get tag id
    $toUntag_sql = "SELECT id FROM tags WHERE (genre = :genre)";
    $toUntag_params = array (
        ":genre" => $toRemove
    );
    $toUntag = exec_sql_query($db, $toUntag_sql, $toUntag_params)->fetchAll(PDO::FETCH_ASSOC);
    $toUntag = $toUntag[0]['id'];

    //select all of album's tag ids
    $infors_sql = "SELECT tag_id FROM album_tags WHERE (album_id = :album_id)";
    $infors_params = array (
        ":album_id" =>$albumId
    );
    $infors = exec_sql_query($db, $infors_sql, $infors_params)->fetchAll(PDO::FETCH_ASSOC);
    $info_array = [];
    foreach ($infors as $infor){
        array_push($info_array, $infor['tag_id']);
    }

    //check if this album already has this tag
    if(!in_array($toUntag, $info_array) && !empty($toUntag)){
        $valid = FALSE;
        array_push($messages, "This album has not been tagged with this genre. Select a different genre or album if you would like.");
    }

    if ($valid) {
        $removeFrom_sql = "DELETE FROM album_tags WHERE (album_id = :album_id) AND (tag_id = :tag_id)";
        $removeFrom_params = array (
            ":album_id" => $albumId[0]['id'],
            ":tag_id" => $toUntag
        );
        $removeFrom_result = exec_sql_query($db, $removeFrom_sql, $removeFrom_params);
        if ($removeFrom_result) {
            array_push($messages, "The album was successfully untagged from this genre.");
        } else {
            array_push($messages, "Could not untag due to invalid input. Please try again.");
        }
    }
}
// ------------------------------------------------------------------------------------------------------ //
// NEW TAG
$show_newGenre_feedback = FALSE;
$show_toNewTag_feedback = FALSE;
if (isset($_POST["newTag"])) {
    $valid = TRUE;

    //filter input
    $newGenre = trim($_POST['newGenre']);
    //check against existing tags
    if(empty($newGenre) || in_array($newGenre, $tags_array)){
        $valid = FALSE;
        $show_newGenre_feedback = TRUE;
    }

    $toNewTag = trim($_POST['toNewTag']);
    //get album from form input
    $attach_id_sql = "SELECT id FROM albums WHERE (title = :title)";
    $attach_id_params = array (
        ":title" => $toNewTag
    );
    $attach_id = exec_sql_query($db, $attach_id_sql, $attach_id_params)->fetchAll(PDO::FETCH_ASSOC);

    if(empty($toNewTag) || !$attach_id){
        $valid = FALSE;
        $show_toNewTag_feedback = TRUE;
    }

    if ($valid) {
        //insert new tag
        $tag_sql = "INSERT INTO tags (genre) VALUES (:genre)";
        $tag_params = array(':genre' => $newGenre);
        $tag_result = exec_sql_query($db, $tag_sql, $tag_params);

        //attach to album
        $album_tag_sql = "INSERT INTO album_tags (album_id,tag_id) VALUES (:album_id, :tag_id)";
        $album_tag_params = array(
            ':album_id' => $attach_id[0]["id"],
            ':tag_id'=> $db->lastInsertId("id")
        );
        $album_tag_result = exec_sql_query($db, $album_tag_sql, $album_tag_params);
        array_push($messages, "Successfully created new tag!");
        if ($tag_result && $album_tag_result) {
            array_push($messages, "Your new tag was added to the album!");
        } else {
            array_push($messages, "Unable to add new tag to album. Try again with different input?");
        }
    } else {
        array_push($messages, "Unable to create tag due to invalid input.");
    }
}
// ------------------------------------------------------------------------------------------------------ //
// MAIN PAGE DISPLAY:
?>
<!DOCTYPE html>
<html lang="en">
<?php include("includes/head.php"); ?>
<body>
  <?php include("includes/header.php"); ?>
  <?php include("includes/nav.php");?>
  <main>
  <section id='actions'>
        <div id="left">
            <p><a href='playlist.php'>&lt; Back to Playlist</a></p>
        </div>
        <div id="right">
            <form id="buttons" action="details.php" method="POST" novalidate>
              <button type="submit" name="add_album">+ Add Album</button>
              <button type="submit" name="delete_album">– Delete Album</button>
              <button type="submit" name="tag_album">+ Tag Album</button>
              <button type="submit" name="untag_album">– Untag Album</button>
              <button type="submit" name="create_tag">+ Create Tag</button>
            </form>
        </div>
    </section>
    <?php
// ------------------------------------------------------------------------------------------------------ //
    //album view
    if(!empty($get_album)){
        // get album info
        $records_sql = "SELECT albums.id, artist, title, file_ext, citation FROM albums LEFT OUTER JOIN album_tags ON album_tags.album_id = albums.id LEFT OUTER JOIN tags ON album_tags.tag_id = tags.id WHERE (title =:title)";
        $records_params = array (
            ":title" => $get_album
        );
        $records = exec_sql_query($db, $records_sql, $records_params)->fetchAll(PDO::FETCH_ASSOC);

        // store tags in different variable
        $entries_sql = "SELECT genre FROM albums LEFT OUTER JOIN album_tags ON album_tags.album_id = albums.id LEFT OUTER JOIN tags ON album_tags.tag_id = tags.id WHERE (title = :title)";
        $entries_params = array (
            ":title" =>$get_album
        );
        $entries = exec_sql_query($db, $entries_sql, $entries_params)->fetchAll(PDO::FETCH_ASSOC); ?>

        <div class = 'display'>
        <?php echo "<figure><img src=\"uploads/albums/" . htmlspecialchars($records[0]["id"]) . "." . htmlspecialchars($records[0]["file_ext"]) . "\" alt=\"" . htmlspecialchars($records["file_name"]) . "\" /></figure>"?>

        <div class="single">
            <h2>Album:</h2>
            <p><?php echo htmlspecialchars($records[0]["title"])?></p>

            <h2>Artist:</h2>
            <p><?php echo htmlspecialchars($records[0]["artist"])?></p>

            <h2>Genres:</h2>
            <?php foreach ($entries as $entry) {
                echo "<p><a href=\"details.php?" . http_build_query(array('genre'=>$entry["genre"])) . "\">" . $entry["genre"] . "</a></p>";
            }?>

            <h2>Citation:</h2>
            <p><cite><?php echo htmlspecialchars($records[0]["citation"])?></cite></p>
        </div>
    </div>
    <?php
    }
// ------------------------------------------------------------------------------------------------------ //
// tag/genre view
    elseif(!empty($get_tag)){
        //get tag id
        $getInfo_sql = "SELECT albums.id, artist, title, file_ext, citation FROM albums LEFT OUTER JOIN album_tags ON album_tags.album_id = albums.id LEFT OUTER JOIN tags ON album_tags.tag_id = tags.id WHERE (genre = :genre)";
        $getInfo_params = array (
            ":genre" => $get_tag
        );
        $getInfo = exec_sql_query($db, $getInfo_sql, $getInfo_params)->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($getInfo)){?>
            <div id = 'gallery'>
                <?php foreach ($getInfo as $get) {?>
                <div class = 'column'>
                    <figure>
                        <img src="uploads/albums/<?php echo htmlspecialchars($get["id"])?>.<?php echo htmlspecialchars($get["file_ext"])?>" alt="<?php echo htmlspecialchars($get["title"])?>"/>
                        <cite class='small'><?php echo  htmlspecialchars($get["citation"])?></cite>
                        <figcaption><p class = 'caption'><?php echo htmlspecialchars($get["title"])?> - <strong><?php echo htmlspecialchars($get["artist"])?></strong></p>
                        <?php echo "<p><a class=\"info\" href=\"details.php?" . http_build_query(array('album'=>htmlspecialchars($get["title"]))) . "\">i</a></p>";?>
                        </figcaption>
                    </figure>
                    </div><?php
                }
        echo "</div>";
    } else {
        array_push($messages, "Looks like no albums are currently tagged with this genre.");
    }
    }
// ------------------------------------------------------------------------------------------------------ //
//add_album
    elseif(isset($_POST['add_album']) || isset($_POST['submit_upload'])){
        ?>
        <h3 id='results'>Suggest Music for the Café Playlist:</h3>
        <form id="AddForm" class="general" action="details.php" method="POST" enctype = "multipart/form-data" novalidate>

        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>"/>

        <div class="item">
            <label for="title">Album Title:</label>
            <input id="title" name="title" value ="<?php echo htmlspecialchars($album_title)?>" required />
            <p class="form_feedback <?php echo ($show_title_feedback) ? '' : 'hidden'; ?>">Please provide an album title (one not already in our playlist).</p>
        </div>

        <div class="item">
            <label for="artist">Artist Name:</label>
            <input id="artist" name="artist" value ="<?php echo htmlspecialchars($artist_name)?>" required />
            <p class="form_feedback <?php echo ($show_artist_feedback) ? '' : 'hidden'; ?>">Please provide the artist's name.</p>
        </div>

        <div class="item">
            <label for="album_art">Upload Album Art:</label>
            <input id="album_art" type= "file" name="album_art" accept=".jpg, .jpeg, .png, image/jpg, image/jpeg, image/png">
        </div>

        <div class="item">
        <label for="citation">Image Citation:</label>
            <input id="citation" name="citation" value ="<?php echo htmlspecialchars($citation)?>" required />
            <p class="form_feedback <?php echo ($show_citation_feedback) ? '' : 'hidden'; ?>">Please provide a link or citation.</p>
        </div>

        <div>
            <button type="submit" class="submit" name="submit_upload">Add to the Café Playlist!</button>
        </div>
        </form>
    <?php
    }
// ------------------------------------------------------------------------------------------------------ //
//delete album
elseif (isset($_POST["delete_album"]) || isset($_POST["deleteAlbum"])){?>
    <h3>Delete an Album from the Café Playlist:</h3>
    <form id="DeleteForm" class="general padded" action="details.php" method="post" novalidate>

        <div class="item">
            <label for="toDelete">Delete:</label>
            <select name="toDelete" id="toDelete" required>
            <option value="">Select Album...</option>
            <?php
            $all_albums = exec_sql_query($db, "SELECT title FROM albums")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($all_albums as $one_album) {
                echo "<option value=\"" . htmlspecialchars($one_album["title"]) . "\">" . $one_album["title"] . "</option>";
            }?>
            </select>
            <p class="form_feedback <?php echo ($show_toDelete_feedback) ? '' : 'hidden';?>">Please pick a tag.</p>
        </div>

        <div>
            <button type='submit' class='submit' id = 'deleteAlbum' name ='deleteAlbum'>Delete Album</button>
        </div>
    </form>
    <?php
}
// ------------------------------------------------------------------------------------------------------ //
// tag_album
    elseif(isset($_POST["tag_album"]) || isset($_POST["addTag"])){
        $tags = exec_sql_query($db, "SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);?>
        <h3>Add a Tag to an Album:</h3>
        <form id="AddTagForm" class="general padded" action="details.php" method="post" novalidate>
            <div class="item">
                <label for="addTo">Album:</label>
                <select name="addTo" id="addTo" required>
                    <option value="">Select album...</option>
                    <?php
                    $all_albums = exec_sql_query($db, "SELECT title FROM albums")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($all_albums as $one_album) {
                        echo "<option value=\"" . htmlspecialchars($one_album["title"]) . "\">" .  htmlspecialchars($one_album["title"]) . "</option>
                        ";
                    }?>
                </select>
                <p class="form_feedback <?php echo ($show_addTo_feedback) ? '' : 'hidden';?>">Please select an album already on our playlist.</p>
            </div>
            <div class="item">
                <label for="tagToTag">Genre:</label>
                <select name="tagToTag" id="tagToTag" required>
                <option value="">Select tag...</option>
                <?php foreach ($tags as $tag) {
                    echo "<option value='" . $tag["genre"] . "'>" . $tag["genre"] . "</option>";
                }?>
                </select>
                <p class="form_feedback <?php echo ($show_tagToTag_feedback) ? '' : 'hidden';?>">Please select a tag.</p>
            </div>
            <div>
                <button type="submit" class="submit" name='addTag'>Add Tag to Album</button>
            </div>
        </form>
    <?php
    }
// ------------------------------------------------------------------------------------------------------ //
//untag album
    elseif (isset($_POST["unTag"]) || isset($_POST["untag_album"])){
        $tags = exec_sql_query($db, "SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);?>
        <h3>Untag an Album from a Genre:</h3>
        <form id="UntagForm" class="general padded" action="details.php" method="post" novalidate>
            <div class="item">
                <label for="removeFrom">Album:</label>
                <select name="removeFrom" id="removeFrom" required>
                    <option value="">Select album...</option>
                    <?php
                    $all_albums = exec_sql_query($db, "SELECT title FROM albums")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($all_albums as $one_album) {
                        echo "<option value=\"" . htmlspecialchars($one_album["title"]) . "\">" .  htmlspecialchars($one_album["title"]) . "</option>
                        ";
                    }?>
                </select>
                <p class="form_feedback <?php echo ($show_removeFrom_feedback) ? '' : 'hidden';?>">Please select an album on our playlist.</p>
            </div>
            <div class="item">
                <label for="toRemove">Genre:</label>
                <select name="toRemove" id="toRemove" required>
                <option value="">Select tag...</option>
                <?php foreach ($tags as $tag) {
                    echo "<option value='" . $tag["genre"] . "'>" . $tag["genre"] . "</option>";
                }?>
                </select>
                <p class="form_feedback <?php echo ($show_toRemove_feedback) ? '' : 'hidden';?>">Please select a tag.</p>
            </div>
            <div>
                <button type="submit" class="submit" name='unTag'>Untag Album</button>
            </div>
        </form>
    <?php
    }
// ------------------------------------------------------------------------------------------------------ //
// create_tag
elseif(isset($_POST["create_tag"]) || isset($_POST["newTag"])){?>
    <h3>Create a New Genre Tag:</h3>

    <form id="TagForm" class="general padded" action="details.php" method="post" novalidate>
        <div class="item">
            <label for="newGenre">Genre:</label>
            <input id="newGenre" name="newGenre" value = "<?php echo htmlspecialchars($newGenre)?>" required/>
            <p class="form_feedback <?php echo ($show_newGenre_feedback) ? '' : 'hidden';?>">Please provide a new genre not already tagged.</p>
        </div>
        <div class="item">
            <label for="toNewTag">Album:</label>
            <select name="toNewTag" id="toNewTag" required>
                <option value="">Select album...</option>
                <?php
                $all_albums = exec_sql_query($db, "SELECT title FROM albums")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($all_albums as $one_album) {
                    echo "<option value=\"" . htmlspecialchars($one_album["title"]) . "\">" . $one_album["title"] . "</option>";
                }?>
            </select>
            <p class="form_feedback <?php echo ($show_toNewTag_feedback) ? '' : 'hidden';?>">Please select an album already on our playlist.</p>
        </div>
        <div>
            <button type="submit" class="submit" name ='newTag' id='newTag'>Create</button>
        </div>
    </form>

    <?php
    }
// ------------------------------------------------------------------------------------------------------ //
    else {
        echo "<p>Hmm... You've found a mysterious page behind the curtain. Nothing to see back here!</p>";
    }
foreach ($messages as $message) {
        echo "<p id=\"message\">" . htmlspecialchars($message) . "</p>\n";
    }
    ?>

  </main>
  <?php include("includes/footer.php"); ?>
</body>

</html>
