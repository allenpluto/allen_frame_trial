DROP TABLE IF EXISTS `tbl_view_organization`;
CREATE TABLE `tbl_view_organization` AS
  SELECT Listing.id,Listing.friendly_url AS friendly_uri,Listing.account_id,Listing.keywords,Listing.description,Listing.long_description,Listing.phone,Listing.email,Listing.url as website,Listing.hours_work,TRIM(CONCAT(Listing.address,' ',Listing.address2)) AS street_address,Listing.city AS suburb,Listing.region AS region,Listing.state,Listing.zip_code AS post,Listing.avg_review, ROUND(avg_review*20,2) as avg_review_percentage,Listing.count_review,Listing.number_views as count_visit,Listing_Gallery.id as image_id,Listing.title as name,Listing.latitude,Listing.longitude,Listing_Banner.id as banner_id, Listing_Logo.id as logo_id,ListingCategory.page_title AS category_name,ListingCategory.schema_itemtype FROM Listing Listing
    LEFT JOIN Image Listing_Banner ON Listing.banner_id = Listing_Banner.id
    LEFT JOIN Image Listing_Logo ON Listing.image_id = Listing_Logo.id
    LEFT JOIN Listing_Category Listing_Category ON Listing.id = Listing_Category.listing_id
    LEFT JOIN ListingCategory ListingCategory ON ListingCategory.id = Listing_Category.category_id
    LEFT JOIN Gallery_Item Gallery_Item ON (Gallery_Item.item_type = 'listing' AND Gallery_Item.item_id = Listing.id)
    LEFT JOIN Gallery_Image Gallery_Image ON Gallery_Image.gallery_id = Gallery_Item.gallery_id
    LEFT JOIN Image Listing_Gallery ON Listing_Gallery.id = Gallery_Image.image_id
  WHERE LOWER(Listing.status) = 'a'
  GROUP BY Listing.id