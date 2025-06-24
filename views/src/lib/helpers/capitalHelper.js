/** capitalize first letter. ex: product -> Product */
export const capitalizeFirstLetter = (name) => {
  return name?.charAt(0)?.toUpperCase() + name?.slice(1);
}