document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarToggleIcon = sidebarToggle ? sidebarToggle.querySelector('i') : null;
    const desktopSidebar = window.matchMedia('(min-width: 1200px)');

    const applySidebarState = (collapse, persist = true) => {
        const shouldCollapse = collapse && desktopSidebar.matches;
        body.classList.toggle('sidebar-collapsed', shouldCollapse);
        if (sidebarToggleIcon) {
            sidebarToggleIcon.classList.toggle('bi-chevron-double-right', shouldCollapse);
            sidebarToggleIcon.classList.toggle('bi-chevron-double-left', !shouldCollapse);
        }
        if (sidebarToggle) {
            const label = shouldCollapse ? 'Espandi barra laterale' : 'Comprimi barra laterale';
            sidebarToggle.setAttribute('aria-label', label);
            sidebarToggle.setAttribute('title', shouldCollapse ? 'Mostra barra laterale' : 'Riduci barra laterale');
        }
        if (persist) {
            localStorage.setItem('ghostSidebarCollapsed', collapse ? '1' : '0');
        }
    };

    const storedPref = localStorage.getItem('ghostSidebarCollapsed') === '1';
    applySidebarState(storedPref, false);

    sidebarToggle?.addEventListener('click', () => {
        const nextState = !body.classList.contains('sidebar-collapsed');
        applySidebarState(nextState);
    });

    if (desktopSidebar.addEventListener) {
        desktopSidebar.addEventListener('change', () => {
            const preferCollapsed = localStorage.getItem('ghostSidebarCollapsed') === '1';
            applySidebarState(preferCollapsed, false);
        });
    } else if (desktopSidebar.addListener) {
        desktopSidebar.addListener(() => {
            const preferCollapsed = localStorage.getItem('ghostSidebarCollapsed') === '1';
            applySidebarState(preferCollapsed, false);
        });
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    const contractForm = document.getElementById('contractForm');
    if (contractForm) {
        const MAX_FILE_SIZE = 10 * 1024 * 1024;
        const contractTypeSelect = document.getElementById('contractType');
        const paymentSelect = document.getElementById('paymentMethod');
        const contractBlocks = contractForm.querySelectorAll('.conditional-block');
        const paymentBlocks = contractForm.querySelectorAll('.conditional-payment');
        const formErrors = document.getElementById('formErrors');
        const submitBtn = document.getElementById('submitBtn');
        const submitSpinner = document.getElementById('submitSpinner');
        const nameInput = document.getElementById('customerName');
        const surnameInput = document.getElementById('customerSurname');
        const ibanHolderInput = document.getElementById('ibanHolder');
        const cardHolderInput = document.getElementById('cardHolder');

        const toggleBlocks = (value, blocks, attr) => {
            blocks.forEach(block => {
                const mustShow = block.dataset[attr] === value;
                block.classList.toggle('active', mustShow);
                block.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = !mustShow;
                });
            });
        };

        toggleBlocks(contractTypeSelect.value, contractBlocks, 'contract');
        toggleBlocks(paymentSelect.value, paymentBlocks, 'payment');

        contractTypeSelect.addEventListener('change', event => {
            toggleBlocks(event.target.value, contractBlocks, 'contract');
        });
        paymentSelect.addEventListener('change', event => {
            toggleBlocks(event.target.value, paymentBlocks, 'payment');
        });

        const autoFillHolder = () => {
            const fullName = `${nameInput.value.trim()} ${surnameInput.value.trim()}`.trim();
            if (fullName) {
                if (ibanHolderInput && !ibanHolderInput.value) {
                    ibanHolderInput.value = fullName;
                }
                if (cardHolderInput && !cardHolderInput.value) {
                    cardHolderInput.value = fullName;
                }
            }
        };
        nameInput?.addEventListener('blur', autoFillHolder);
        surnameInput?.addEventListener('blur', autoFillHolder);

        const validateCodiceFiscale = value => /^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/.test(value.toUpperCase());
        const validateIban = value => /^[A-Z]{2}[0-9A-Z]{13,30}$/.test(value.replace(/\s+/g, '').toUpperCase());
        const luhnCheck = value => {
            const sanitized = value.replace(/\s+/g, '');
            if (!/^[0-9]{13,19}$/.test(sanitized)) return false;
            let sum = 0;
            let shouldDouble = false;
            for (let i = sanitized.length - 1; i >= 0; i -= 1) {
                let digit = parseInt(sanitized.charAt(i), 10);
                if (shouldDouble) {
                    digit *= 2;
                    if (digit > 9) digit -= 9;
                }
                sum += digit;
                shouldDouble = !shouldDouble;
            }
            return sum % 10 === 0;
        };

        const validateDates = () => {
            const birthDate = document.getElementById('birthDate');
            const issueDate = document.getElementById('documentRelease');
            const expiryDate = document.getElementById('documentExpiry');
            const today = new Date().setHours(0, 0, 0, 0);
            const errors = [];
            if (birthDate?.value && new Date(birthDate.value).getTime() > today) {
                errors.push('La data di nascita non può essere futura.');
            }
            if (issueDate?.value && expiryDate?.value) {
                const issue = new Date(issueDate.value).getTime();
                const expiry = new Date(expiryDate.value).getTime();
                if (issue > expiry) {
                    errors.push('La data di rilascio del documento deve precedere la scadenza.');
                }
                if (expiry < today) {
                    errors.push('La scadenza del documento deve essere futura.');
                }
            }
            return errors;
        };

        const validateFiles = () => {
            const errors = [];
            contractForm.querySelectorAll('input[type="file"]').forEach(input => {
                Array.from(input.files || []).forEach(file => {
                    if (file.size > MAX_FILE_SIZE) {
                        errors.push(`Il file "${file.name}" supera i 10MB.`);
                    }
                });
            });
            return errors;
        };

        const showErrors = messages => {
            if (!messages.length) {
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                return;
            }
            formErrors.classList.remove('d-none');
            formErrors.innerHTML = `<strong>Correggi i seguenti errori:</strong><ul class="mb-0"><li>${messages.join('</li><li>')}</li></ul>`;
            window.scrollTo({ top: formErrors.offsetTop - 80, behavior: 'smooth' });
        };

        contractForm.addEventListener('submit', event => {
            const errors = [];
            const cfInput = document.getElementById('cf');
            if (cfInput && !validateCodiceFiscale(cfInput.value)) {
                errors.push('Inserisci un codice fiscale valido.');
                cfInput.classList.add('is-invalid');
            }

            errors.push(...validateDates());
            errors.push(...validateFiles());

            if (contractTypeSelect.value === 'telefonia') {
                const migrationInput = document.getElementById('migrationCode');
                const lineInput = contractForm.querySelector('input[name="tel_line_number"]');
                if (lineInput?.value.trim() && !migrationInput?.value.trim()) {
                    errors.push('Fornisci il codice di migrazione per la portabilità.');
                }
            }

            if (paymentSelect.value === 'rid') {
                const ibanInput = document.getElementById('iban');
                if (!validateIban(ibanInput.value)) {
                    errors.push('Inserisci un IBAN valido.');
                    ibanInput.classList.add('is-invalid');
                }
                if (!ibanHolderInput.value.trim()) {
                    errors.push('Specifica l\'intestatario del conto.');
                }
                if (!document.getElementById('sddConsent').checked) {
                    errors.push('Devi autorizzare il mandato SDD.');
                }
            }

            if (paymentSelect.value === 'carta') {
                const cardNumber = document.getElementById('cardNumber');
                const cardExpiry = document.getElementById('cardExpiry');
                const cardCvv = document.getElementById('cardCvv');
                if (!luhnCheck(cardNumber.value)) {
                    errors.push('Numero carta non valido.');
                    cardNumber.classList.add('is-invalid');
                }
                if (!/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(cardExpiry.value)) {
                    errors.push('Inserisci la scadenza nel formato MM/YY.');
                    cardExpiry.classList.add('is-invalid');
                }
                if (!/^[0-9]{3}$/.test(cardCvv.value)) {
                    errors.push('Il CVV deve contenere 3 cifre.');
                    cardCvv.classList.add('is-invalid');
                }
                if (!cardHolderInput.value.trim()) {
                    errors.push('L\'intestatario carta è obbligatorio.');
                }
            }

            if (errors.length) {
                event.preventDefault();
                event.stopPropagation();
                showErrors(errors);
                submitSpinner?.classList.add('d-none');
                submitBtn?.removeAttribute('data-loading');
            } else {
                formErrors.classList.add('d-none');
                submitSpinner?.classList.remove('d-none');
                submitBtn?.setAttribute('disabled', 'disabled');
            }
        });
    }

    if (window.Chart) {
        const chartEl = document.getElementById('contractsChart');
        if (chartEl) {
            const ctx = chartEl.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(42, 108, 242, 0.25)');
            gradient.addColorStop(1, 'rgba(42, 108, 242, 0)');
            new window.Chart(chartEl, {
                type: 'line',
                data: {
                    labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu'],
                    datasets: [{
                        label: 'Contratti',
                        data: [12, 19, 8, 17, 23, 30],
                        borderColor: '#2A6CF2',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {legend: {display: false}},
                    scales: {
                        x: {grid: {display: false}},
                        y: {grid: {color: 'rgba(42,108,242,0.08)', drawBorder: false}}
                    }
                }
            });
        }

        if (window.reportData) {
            const typeCtx = document.getElementById('typeChart');
            if (typeCtx) {
                const byType = {};
                window.reportData.forEach(row => {
                    byType[row.type] = (byType[row.type] || 0) + parseInt(row.total, 10);
                });
                new window.Chart(typeCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(byType),
                        datasets: [{
                            data: Object.values(byType),
                            backgroundColor: ['#2A6CF2', '#4BC0F8', '#22C1A4', '#FFB347'],
                            borderRadius: 6
                        }]
                    }
                });
            }
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const byStatus = {};
                window.reportData.forEach(row => {
                    const key = row.status || 'N/D';
                    byStatus[key] = (byStatus[key] || 0) + parseInt(row.total, 10);
                });
                new window.Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(byStatus),
                        datasets: [{
                            data: Object.values(byStatus),
                            backgroundColor: ['#2A6CF2','#4BC0F8','#22C1A4','#FF6B81','#D5DCE6']
                        }]
                    }
                });
            }
        }
    }
});
