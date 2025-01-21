#!/bin/bash

# Get the current date in seconds since epoch
current_date=$(date +%s)

# Get the date 6 months ago in seconds since epoch
six_months_ago=$(date --date='6 months ago' +%s)

# List all images with their creation date
docker images --format '{{.Repository}}:{{.Tag}} {{.ID}} {{.CreatedAt}}' | while read image; do
  # Extract the image ID and creation date
  image_id=$(echo $image | awk '{print $2}')
  created_at=$(echo $image | awk '{print $3" "$4" "$5}')

  # Convert the creation date to seconds since epoch
  created_at_seconds=$(date --date="$created_at" +%s)

  # Check if the creation date is older than 6 months
  if [ $created_at_seconds -lt $six_months_ago ]; then
    # Remove the image
    echo "Removing image $image_id created at $created_at"
    docker rmi $image_id
  fi
done

