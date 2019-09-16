<<?php 
trace($results);

 ?>
<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

        <h1><?php echo $results['pageTitle']?></h1>

        <form action="admin.php?action=<?php echo $results['formAction']?>" method="post"> 
          <!-- Обработка формы будет направлена файлу admin.php ф-ции newCategory либо editCategory в зависимости от formAction, сохранённого в result-е -->
        <input type="hidden" name="categoryId" value="<?php echo $results['subCategories']->id ?>"/>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

        <ul>

          <li>
            <label for="name">SubCategory Name</label>
            <input type="text" name="name" id="name" placeholder="Name of the category" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['subCategories']->name )?>" />
          </li>

          <li>
            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Brief description of the subCategory" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars( $results['subCategories']->description )?></textarea>
          </li>

          <select name='parentCategory'>
            <?php 
              foreach ($results['category'] as $category) 
              {
                echo "<option>".$category->name."</option>";
              }
            ?>
          </select>

        </ul>

        <div class="buttons">
          <input type="submit" name="saveChanges" value="Save Changes" />
          <input type="submit" formnovalidate name="cancel" value="Cancel" />
        </div>

      </form>

    <?php if ( $results['subCategories']->id ) { ?>
          <p><a href="admin.php?action=deleteSubCategory&amp;subCategoryId=<?php echo $results['subCategories']->id ?>" onclick="return confirm('Delete This Category?')">Delete This SubCategory</a></p>
    <?php } ?>

<?php include "templates/include/footer.php" ?>

