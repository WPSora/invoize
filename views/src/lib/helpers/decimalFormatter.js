/** 
 * @param {string} currencyName 
 * @param {number} value
 * @returns {string}
*/
export const numberFormatter = (currencyName, value) => {
  if (!currencyName || currencyName === 'none') return;
  // eg: 1000 -> 1.000
  if (currencyName === "IDR") {
    const idrFormatter = new Intl.NumberFormat("id", {
      style: "decimal",
      currency: "IDR",
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    });
    return idrFormatter.format(value);
  }
  // eg: 1000 -> 1,000
  const nonIdrFormatter = new Intl.NumberFormat("en-US", {
    style: "decimal",
    currency: currencyName,
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
  return nonIdrFormatter.format(value);
}

/** 
 * @param {string} currencyName 
 * @param {number} value
 * @returns {string}
*/
export const currencyFormatter = (currencyName, value, minDigit = 0) => {
  if (!currencyName || currencyName === 'none') return;
  // eg: 1000 -> Rp 1.000
  const currency = currencyName === "IDR" ? "id" : "en-US";
  const formatter = new Intl.NumberFormat(currency, {
    style: "currency",
    currency: currencyName,
    minimumFractionDigits: minDigit,
    maximumFractionDigits: 2,
  });
  return formatter.format(value);
}