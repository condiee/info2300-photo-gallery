# Project 3: Well-Designed Photo Gallery

- Develop a professional interactive website suitable for a portfolio.
- Develop the skills to translate client/customer requirements into a working implementation.
- Leverage design patterns to improve the usability of your site.
- Practice using a _web programmer's toolkit_ to solve complex problems.
- Practice structuring a database with multiple tables and foreign keys.
- Practice building and querying relationships between tables using common fields (joins).
- Employ best practices for user uploaded content for dynamic websites.

Designed and implemented an online photo gallery in PHP. Demonstrated ability to design an aesthetically pleasing and usable interactive site. Photo gallery is backed by a database used to store information about the images uploaded to the gallery. Implemented the ability for users of your site to tag the photos to help organize the photos in your gallery.

## Photo Gallery

- Users can view *all* images in your photo gallery at once. (e.g. a gallery/thumbnail page)
- Users can view *all* images for a *tag* at once. (e.g. tag page or filter by tag on gallery page)
- Users can view a *single image* and all the tags for that image at once. (e.g. image details page)
- Users can upload a new image.
- Users can remove (delete) an image.
  - Cleans up any relationships to the image in other tables. (Where the image is a foreign key.)
  - Deletes the corresponding file upload from disk.
- Users can view *all* tags at once. (e.g. a list of tags)
- Users can add an existing tag to an image, add a new tag to an image, and remove a tag from an image.
- Tags must be unique. You cannot have duplicates of the same tag.
