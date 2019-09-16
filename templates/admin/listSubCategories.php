<?php include "templates/include/header.php" ?>
	<?php include "templates/admin/include/header.php" ?>
	  
            <h1>SubCategories Category</h1>
	  
	<?php if ( isset( $results['errorMessage'] ) ) { ?>
	        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
	<?php } ?>
	  
	  
	<?php if ( isset( $results['statusMessage'] ) ) { ?>
	        <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
	<?php } ?>
	  
            <table>
                <tr>
                    <th>Родительская Категория</th>
                    <th>Название(под категории)</th>
                    <th>Описание</th>
                </tr>

        <?php foreach ( $results['subCategories'] as $category ) { ?>

                <tr onclick="location='admin.php?action=editSubCategories&amp;subCategoryId=<?php echo $category->id?>'">
                    <td>
                        <?php echo $category->parentCategory?>
                    </td>
   
                    <td>
                        <?php echo $category->name?>
                    </td>

                    <td>
                        <?php echo $category->description?>
                    </td>
                </tr>

        <?php } ?>

            </table>

            <p><?php echo $results['totalRows']?> categor<?php echo ( $results['totalRows'] != 1 ) ? 'ies' : 'y' ?> in total.</p>

            <p><a href="admin.php?action=newSubCategory">Add a New SubCategory</a></p>
	  
	<?php include "templates/include/footer.php" ?>
