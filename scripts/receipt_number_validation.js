function validateReceiptNumber() {
    const receiptNumberInput = document.getElementById('receipt_number');
    const validationDiv = document.getElementById('receipt_number_validation');
    const value = receiptNumberInput.value;

    if (value.includes('ORD-') || value.includes('RET-')) {
        validationDiv.textContent = 'Ez egy rendelésszám, a bizonylat számát add meg!';
        validationDiv.className = 'validation error';
    } else if (value.startsWith('A') && !value.includes('/')) {
        validationDiv.textContent = 'Ez (eddig csak) az adóügyi nyomtató száma, de / utána megadva a nyugta számát már jó lesz';
        validationDiv.className = 'validation uncertain';
    } else if (value.startsWith('M/')) {
        validationDiv.textContent = 'Ez egy pénzmozgás bizonylat száma, a számla számát add meg!';
        validationDiv.className = 'validation error';
    } else if (value.match(/[ ,;]/)) {
        validationDiv.textContent = 'Van nem odaillő karakter a mezőben, például vessző vagy szóköz';
        validationDiv.className = 'validation error';
    } else if (value.match(/^\d+\/\d+$/)) {
        validationDiv.textContent = 'Rendben, ez a nyugta száma';
        validationDiv.className = 'validation success';
    } else if (value.length >= 4 && value.length <= 6 && !value.includes('/')) {
        validationDiv.textContent = 'Nem jó, ez egy NAV ellenőrző kód, a nyugta vagy a számla számát add meg!';
        validationDiv.className = 'validation error';
    } else if (value.startsWith('A') && (value.match(/\//g) || []).length >= 2) {
        validationDiv.textContent = 'Rendben, ez egy nyugta száma';
        validationDiv.className = 'validation success';
    } else if (value.match(/^E-FP\d{2}-\d{4}-\d+$/) || value.match(/^FP[A-Z]{0,2}-\d{4}-\d+$/)) {
        validationDiv.textContent = 'Rendben, ez egy számlaszám';
        validationDiv.className = 'validation success';
    } else {
        validationDiv.textContent = 'A mező értékét nem ismertük fel (még)';
        validationDiv.className = 'validation uncertain';
    }
}

document.getElementById('receipt_number').addEventListener('input', validateReceiptNumber);
