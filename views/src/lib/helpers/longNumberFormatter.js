/* Add K if more than 1000 or add M if more than 1000000 */
export const formatLongNumber = (value) => {
  if (!value) return 0;
  if (value == 0) return 0;

  const num = parseInt(value);
  let formattedNumber;
  if (num >= 1000000000) {
    formattedNumber = Math.floor(num / 10000000) / 100;
    formattedNumber = formattedNumber.toString().replace(".", ",");
    if (formattedNumber.endsWith(",00")) {
      formattedNumber = formattedNumber.slice(0, -3);
    } else if (formattedNumber.endsWith("0")) {
      formattedNumber = formattedNumber.slice(0, -1);
    }
    return formattedNumber + "B";
  } else if (num >= 1000000) {
    formattedNumber = Math.floor(num / 10000) / 100;
    formattedNumber = formattedNumber.toString().replace(".", ",");
    if (formattedNumber.endsWith(",00")) {
      formattedNumber = formattedNumber.slice(0, -3);
    } else if (formattedNumber.endsWith("0")) {
      formattedNumber = formattedNumber.slice(0, -1);
    }
    return formattedNumber + "M";
  } else if (num >= 1000) {
    formattedNumber = Math.floor(num / 10) / 100;
    formattedNumber = formattedNumber.toString().replace(".", ",");
    if (formattedNumber.endsWith(",00")) {
      formattedNumber = formattedNumber.slice(0, -3);
    } else if (formattedNumber.endsWith("0")) {
      formattedNumber = formattedNumber.slice(0, -1);
    }
    return formattedNumber + "K";
  } else {
    return num.toString();
  }
};