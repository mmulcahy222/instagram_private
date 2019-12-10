# Instagram API

This is tied in my top 2 favorite applications which I made! I use this all the time.

For important reasons, I'm not giving out the code for this except for the Instagram Authentication code which was truly difficult. If you are an employer and you would love to see the code I did the sites below, please contact me at opaline222@gmail.com or on GitHub or on any of my social media accounts.

This offers a much augmented better Instagram experience that is custom to my needs. It can search hashtags & locations, and it offers one-click AJAX functionality for Liking/Following/Inboxing/Saving other users, with an animated fading notification for "Success/Fail" below resembling Facebook's notifications (Red for fail, Green for success)

Pressing the keyboard will iterate the images to the next pictures, and they will be preloaded ahead of time so they don't slowly come up after a keyboard press.

```js
//KEY PRESS TO MOVE FORWARD
$( document ).keypress(function(e) {
	var tag = e.target.tagName.toLowerCase();
	if (tag != 'input')
	{
		key_code = e.keyCode;
		if(key_code == 113)
		{
			offset -= length * 2;
		}
		$("#container").html("");
		sliced_divs = divs.slice(offset,offset+length);
		$(divs[0]).find('.thumbnail_image').attr('src')
		//start filling array for current_user_ids (see all pictures)
		current_user_ids = [];
		current_location_ids = [];
		sliced_divs.forEach(function(entry) {
			$("#container").append(entry);
			$('a',entry).each(function(index, el) {
				url = $(this).attr('href');
				//link name has instagram_user in instagram_user.php
				if(url.includes('instagram_user'))
				{
					user_id = get_first_match(url, /\d{5,}/g);
					if(user_id != null&&current_user_ids.includes(user_id) == false)
					{
						current_user_ids.push(user_id);
					}
				}
				else if(url.includes('instagram_location'))
				{
					location_id = get_first_match(url,/\d{5,}/g);
					if(location_id != null&&current_location_ids.includes(location_id) == false)
					{
						current_location_ids.push(location_id);
					}
				}
			});
		});
		//end filling for current_user_ids
		//start preload
		preload_count = 0;
		preloaded_sliced_divs = divs.slice(offset+length, offset+(length*preload_depth));
		preloaded_sliced_divs.forEach(function(entry) {
			thumbnail_link = $(entry).find('.thumbnail_image').attr('src');
			preloaded_images[preload_count].src = thumbnail_link;
			preload_count += 1;
		});
		//end preload
		offset += length;
	}
});
```

There are more features, just look on below.

# Authentication

I had many ways of persisting the session instead of logging into Instagram in every single Instagram API Call. Since PHP closures do not serialize, I had to change the session object once it's created just to successfully serialize the session object to eliminate continual login calls. You could check it out in instagram_authentication

# Hashtag

![](images/hashtag.png)

# Location

Anytime a user accesses a new picture through a hashtag/location search, it saves a location as an entry in an SQLite Database. But it leverages SQL to determine if it is already in there and will not add if it is.

```php
if(isset($location_id) && $_GET['save'])
{
	$file_db = new PDO('sqlite:location.sqlite3');
	$exec_query = "INSERT INTO locations (location_id,location) SELECT '{$location_id}', '{$location_name}' WHERE NOT EXISTS (SELECT 1 FROM locations WHERE location_id = '{$location_id}' AND location = '{$location_name}')";
	$exec = $file_db->exec($exec_query);
}
```

There is an autocomplete in one of the fields to choose a city or location to do this search, instead of searching online for the Location ID.

![](images/location_choices.jpg)
![](images/location.png)

# User 

On the right hand column of the User Page are all the tagged users, which will automatically show up (done in JavaScript Ajax Success calls). It allows doing the Instagram one-click actions faster.

![](images/user.jpg)

# Media

![](images/media.jpg)