<?php include("includes/init.php");
$title = "CAFÉ PLAYLIST";
const MAX_FILE_SIZE = 1000000; // 1MB
?>

<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php"); ?>

<body>
  <?php include("includes/header.php"); ?>
  <?php include("includes/nav.php");?>
  <section id="actions">
      <div id="left"></div>
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
  <main id="playlist">
    <div id="gallery_full">
      <div class="column">
        <?php
        //print image gallery
        $records = exec_sql_query($db, "SELECT DISTINCT albums.id, artist, title, file_ext, citation FROM albums LEFT OUTER JOIN album_tags ON album_tags.album_id = albums.id")->fetchAll(PDO::FETCH_ASSOC);

        if (count($records) > 0) {
          foreach ($records as $record) {
            echo "<figure class=\"gallery\">
                  <img src=\"uploads/albums/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" alt=\"" . htmlspecialchars($record["title"]) . "\" />

                  <cite class='small'>" . htmlspecialchars($record["citation"]) . "</cite>

                  <figcaption><p class = 'caption'>" . htmlspecialchars($record["title"]) . " - <strong>" . htmlspecialchars($record["artist"]) . "</strong></p>

                  <p><a class=\"info\" href=\"details.php?" . http_build_query(array('album'=>htmlspecialchars($record["title"]))) . "\">i</a></p>

                  </figcaption>
                  </figure>";
          }
        } else {
            echo '<p><strong>Playlist currently empty. Fill out the form to add your recommendations!</strong></p>';
        }
        ?>
      </div>

      <div class="column">
        <div>
          <h3>What's This?</h3>
          <p>We'd like to create the perfect café ambiance for our customers, so if there's any particular music you'd like to hear at Zeus, list it here and we'll add it to our playlist if we like the looks of it! If you'd just like to peruse, click on album art for more details and check out the genres we've tagged.</p>
        </div>

        <div>
          <h3>All Tagged Genres:</h3>
          <?php
          $tags = exec_sql_query($db, "SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);
          ?>
          <ul>
          <?php
          foreach ($tags as $tag) {
            echo "<li><a href=\"details.php?" . http_build_query(array('genre'=>$tag["genre"])) . "\">" . $tag["genre"] . "</a></li>";
          }
          ?>
          </ul>
        </div>
      </div>
    </div>

  </main>
  <?php include("includes/footer.php"); ?>
</body>

</html>
