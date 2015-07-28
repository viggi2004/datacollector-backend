<?php

namespace citibytes;

use citibytes\persister\S3Persister;

/**
 * Deletes photos in S3
 */

class S3PhotoDeleter
{

	/**
	 *	Deletes photos that are in S3 but paths not in SimpleDB
	 *
	 *	When a business is updated, all the photos are uploaded again to S3. The
	 *	name of those photos might be different or the same even if the user
	 *	hasn't added/removed any photos. This might lead to problems like there
	 *	are photos in S3 whose paths aren't in SimpleDB. To circumvent this,
	 *	before saving a business data that is being edited, all photos in S3
	 *	which aren't in the Simple DB are deleted. 
	 *
	 *	@param $old_photo_url_array Photo URL of photos before business data 
	 *															is updated in SimpleDB
	 *	@param $new_photo_url_array Photo URL of photos that are to be updated
	 *															in S3	
	 */
	public static function delete($old_photo_url_array,$new_photo_url_array)
	{
		$to_delete_photo_url_array = array_diff($old_photo_url_array,
																						$new_photo_url_array);
		//If there is no diff, there is no need of deleting
		if(empty($to_delete_photo_url_array) === TRUE)
			return;
		S3Persister::batch_delete($to_delete_photo_url_array);
	}
}

?>
