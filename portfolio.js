// portfolio.js - Complete file with all functionality
let isSubmitting = false;
let autoSaveTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
    initializeEventListeners();
    initializeProfessionalExperience();
    initializeEducationOtherInputs();
    setupAutoSave();
    setupFormSubmission();
    initializeNameFields();
});

// Initialize Professional Experience functionality
function initializeProfessionalExperience() {
    // Load categories based on selected industry
    const coreIndustry = document.getElementById('core_industry');
    if (coreIndustry && coreIndustry.value) {
        loadCategories(coreIndustry.value);
    }
    
    // Set up experience blocks
    setupExperienceBlocks();
    setupProjectManagementToggle();
    setupCountryCodeToggle();
    setupCoreIndustryToggle();
    setupCategoryToggles();
}

// Setup country code toggle
function setupFormSubmission() {
    const form = document.getElementById('portfolioForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        
        isSubmitting = true;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        submitButton.disabled = true;

        try {
            const formData = new FormData(form);
            
            // Add the action type if it's an update
            if (form.getAttribute('data-action') === 'update') {
                formData.append('action', 'update');
            }

            const response = await fetch('action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                const successMessage = form.getAttribute('data-action') === 'update' 
                    ? 'Portfolio updated successfully!' 
                    : 'Portfolio submitted successfully!';
                    
                showNotification(successMessage, 'success');
                
                // Redirect after delay
                setTimeout(() => {
                    window.location.href = 'profile.php';
                }, 2000);
            } else {
                // Show error message
                showNotification(result.message || 'Error processing portfolio. Please try again.', 'error');
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An unexpected error occurred. Please try again.', 'error');
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        }

        isSubmitting = false;
    });
}

function showNotification(message, type) {
    const notificationArea = document.getElementById('formNotificationArea');
    if (!notificationArea) return;

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

    notificationArea.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Scroll to notification
    notificationArea.scrollIntoView({ behavior: 'smooth' });
}

function setupCountryCodeToggle() {
    const countryCodeSelect = document.getElementById('countryCode');
    if (countryCodeSelect) {
        countryCodeSelect.addEventListener('change', function() {
            toggleOther('countryCode', 'countryCodeOtherContainer', 'other');
            saveField('countryCode', this.value);
        });
    }
}

// Setup core industry toggle
function setupCoreIndustryToggle() {
    const coreIndustrySelect = document.getElementById('core_industry');
    if (coreIndustrySelect) {
        coreIndustrySelect.addEventListener('change', function() {
            toggleOther('core_industry', 'coreIndustryOtherContainer', 'other');
            if (this.value && this.value !== 'other') {
                loadCategories(this.value);
            } else {
                document.getElementById('category').innerHTML = '<option value="">Select Category</option>';
                document.getElementById('subcategory').innerHTML = '<option value="">Select Subcategory</option>';
                document.getElementById('skills-container').style.display = 'none';
            }
            saveField('core_industry', this.value);
        });
    }
}

// Setup category and subcategory toggles
function setupCategoryToggles() {
    const categorySelect = document.getElementById('category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            toggleOther('category', 'categoryOtherContainer', 'other');
            if (this.value && this.value !== 'other') {
                loadSubcategories(this.value);
            } else {
                document.getElementById('subcategory').innerHTML = '<option value="">Select Subcategory</option>';
                document.getElementById('skills-container').style.display = 'none';
            }
            saveField('category', this.value);
        });
    }
    
    const subcategorySelect = document.getElementById('subcategory');
    if (subcategorySelect) {
        subcategorySelect.addEventListener('change', function() {
            toggleOther('subcategory', 'subcategoryOtherContainer', 'other');
            if (this.value && this.value !== 'other') {
                loadSkills(this.value);
            } else {
                document.getElementById('skills-container').style.display = 'none';
            }
            saveField('subcategory', this.value);
        });
    }
}

// Setup experience blocks
function setupExperienceBlocks() {
    // US Experience
    const addUsExperienceBtn = document.getElementById('add-us-experience');
    if (addUsExperienceBtn) {
        addUsExperienceBtn.addEventListener('click', function() {
            addExperienceBlock('us');
        });
    }
    
    // India Experience
    const addIndiaExperienceBtn = document.getElementById('add-india-experience');
    if (addIndiaExperienceBtn) {
        addIndiaExperienceBtn.addEventListener('click', function() {
            addExperienceBlock('india');
        });
    }
}

// Setup project management toggle
function setupProjectManagementToggle() {
    document.querySelectorAll('input[name="project_management_exp"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const details = document.getElementById('project-management-details');
            if (this.value === 'yes') {
                details.classList.remove('d-none');
            } else {
                details.classList.add('d-none');
            }
            saveField('project_management_exp', this.value);
        });
    });
}

// Add experience block
function addExperienceBlock(type) {
    const container = document.getElementById(`${type}-experience-container`);
    if (!container) return;
    
    const blocks = container.querySelectorAll('.experience-block');
    const newIndex = blocks.length;
    
    const newBlock = document.createElement('div');
    newBlock.className = 'experience-block';
    newBlock.setAttribute('data-index', newIndex);
    
    newBlock.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="form-label mb-0">Experience #${newIndex + 1}</h6>
            <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, '${type}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Name of Employer</label>
                <input type="text" name="${type}_experience[${newIndex}][employer]" class="form-control experience-input" placeholder="Employer name">
            </div>
            <div class="col-md-6">
                <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                <div class="d-flex gap-2">
                    <input type="text" name="${type}_experience[${newIndex}][from]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                    <span class="align-self-center">to</span>
                    <input type="text" name="${type}_experience[${newIndex}][to]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">State</label>
                <input type="text" name="${type}_experience[${newIndex}][state]" class="form-control experience-input" placeholder="State">
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="${type}_experience[${newIndex}][city]" class="form-control experience-input" placeholder="City">
            </div>
        </div>
    `;
    
    container.appendChild(newBlock);
    
    // Add event listeners to new inputs
    newBlock.querySelectorAll('.experience-input').forEach(input => {
        input.addEventListener('input', function() {
            saveExperienceData(type);
        });
    });
}

// Save experience data
function saveExperienceData(type) {
    const container = document.getElementById(`${type}-experience-container`);
    const blocks = container.querySelectorAll('.experience-block');
    const experienceData = [];
    
    blocks.forEach(block => {
        const index = block.getAttribute('data-index');
        const employer = block.querySelector(`input[name="${type}_experience[${index}][employer]"]`)?.value || '';
        const from = block.querySelector(`input[name="${type}_experience[${index}][from]"]`)?.value || '';
        const to = block.querySelector(`input[name="${type}_experience[${index}][to]"]`)?.value || '';
        const state = block.querySelector(`input[name="${type}_experience[${index}][state]"]`)?.value || '';
        const city = block.querySelector(`input[name="${type}_experience[${index}][city]"]`)?.value || '';
        
        if (employer || from || to || state || city) {
            experienceData.push({
                employer,
                from,
                to,
                state,
                city
            });
        }
    });
    
    saveField(`${type}_experience`, JSON.stringify(experienceData));
}

// Remove experience block
function removeExperienceBlock(button, type) {
    const block = button.closest('.experience-block');
    const container = document.getElementById(`${type}-experience-container`);
    if (!container) return;
    
    const blocks = container.querySelectorAll('.experience-block');
    
    if (blocks.length > 1) {
        block.remove();
        // Reindex remaining blocks
        const remainingBlocks = container.querySelectorAll('.experience-block');
        remainingBlocks.forEach((block, index) => {
            block.setAttribute('data-index', index);
            block.querySelector('h6').textContent = `Experience #${index + 1}`;
            
            // Update input names
            const inputs = block.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
        });
        
        saveExperienceData(type);
    } else {
        // If it's the last block, just clear the inputs
        const inputs = block.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
        });
        saveExperienceData(type);
    }
}

// Dynamic category and subcategory loading
function loadCategories(industry) {
    fetch(`get_categories.php?industry=${industry}`)
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('category');
            if (!categorySelect) return;
            
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            
            if (data.categories) {
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
            }
            
            // Add "Other" option
            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'Other';
            categorySelect.appendChild(otherOption);
            
            document.getElementById('subcategory').innerHTML = '<option value="">Select Subcategory</option>';
            document.getElementById('skills-container').style.display = 'none';
        })
        .catch(error => console.error('Error loading categories:', error));
}

function loadSubcategories(categoryId) {
    fetch(`get_subcategories.php?category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            const subcategorySelect = document.getElementById('subcategory');
            if (!subcategorySelect) return;
            
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (data.subcategories) {
                data.subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
            }
            
            // Add "Other" option
            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'Other';
            subcategorySelect.appendChild(otherOption);
            
            document.getElementById('skills-container').style.display = 'none';
        })
        .catch(error => console.error('Error loading subcategories:', error));
}

function loadSkills(subcategoryId) {
    fetch(`get_skills.php?subcategory_id=${subcategoryId}`)
        .then(response => response.json())
        .then(data => {
            const skillsList = document.getElementById('skills-list');
            if (!skillsList) return;
            
            skillsList.innerHTML = '';
            
            if (data.skills) {
                data.skills.forEach(skill => {
                    const skillChip = document.createElement('div');
                    skillChip.className = 'skill-chip';
                    skillChip.textContent = skill.name;
                    skillChip.setAttribute('data-skill', skill.name);
                    skillChip.addEventListener('click', function() {
                        toggleSkill(this);
                    });
                    skillsList.appendChild(skillChip);
                });
                
                document.getElementById('skills-container').style.display = 'block';
            }
        })
        .catch(error => console.error('Error loading skills:', error));
}

function toggleSkill(skillChip) {
    const skillName = skillChip.getAttribute('data-skill');
    const selectedSkillsList = document.getElementById('selected-skills-list');
    const selectedSkillsContainer = document.getElementById('selected-skills-container');
    
    if (!selectedSkillsList || !selectedSkillsContainer) return;
    
    if (skillChip.classList.contains('selected')) {
        // Remove skill
        skillChip.classList.remove('selected');
        const skillInput = selectedSkillsList.querySelector(`input[value="${skillName}"]`);
        if (skillInput) {
            skillInput.closest('.skill-chip').remove();
        }
    } else {
        // Add skill
        skillChip.classList.add('selected');
        const skillElement = document.createElement('div');
        skillElement.className = 'skill-chip selected';
        skillElement.setAttribute('data-skill', skillName);
        skillElement.innerHTML = `
            ${skillName}
            <input type="hidden" name="selected_skills[]" value="${skillName}">
            <span class="ms-2 cursor-pointer" onclick="removeSkill(this)">×</span>
        `;
        selectedSkillsList.appendChild(skillElement);
        selectedSkillsContainer.classList.remove('d-none');
    }
    
    // Save selected skills
    const selectedSkills = Array.from(selectedSkillsList.querySelectorAll('input[name="selected_skills[]"]')).map(input => input.value);
    saveField('selected_skills', JSON.stringify(selectedSkills));
}

function removeSkill(removeButton) {
    const skillChip = removeButton.closest('.skill-chip');
    const skillName = skillChip.getAttribute('data-skill');
    
    // Remove from selected skills
    skillChip.remove();
    
    // Update available skills
    const availableSkill = document.querySelector(`#skills-list .skill-chip[data-skill="${skillName}"]`);
    if (availableSkill) {
        availableSkill.classList.remove('selected');
    }
    
    // Hide selected skills container if empty
    const selectedSkillsList = document.getElementById('selected-skills-list');
    if (selectedSkillsList && selectedSkillsList.children.length === 0) {
        const selectedSkillsContainer = document.getElementById('selected-skills-container');
        if (selectedSkillsContainer) {
            selectedSkillsContainer.classList.add('d-none');
        }
    }
    
    // Save selected skills
    const selectedSkills = Array.from(selectedSkillsList.querySelectorAll('input[name="selected_skills[]"]')).map(input => input.value);
    saveField('selected_skills', JSON.stringify(selectedSkills));
}

// Initialize name fields handling
function initializeNameFields() {
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    
    if (firstName && lastName) {
        // Auto-save for name fields
        [firstName, lastName].forEach(field => {
            field.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    saveField(this.name, this.value);
                }, 1000);
            });
        });
    }
}

// Enhanced auto-save functionality
function setupAutoSave() {
    // Text inputs and textareas
    document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="url"], textarea').forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                saveField(this.name, this.value);
            }, 1000);
        });
    });
    
    // Select fields
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function() {
            saveField(this.name, this.value);
        });
    });
    
    // Radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                saveField(this.name, this.value);
            }
        });
    });
    
    // Checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            saveField(this.name, this.checked ? '1' : '0');
        });
    });
    
    // Multiple select
    const locationPreference = document.getElementById('locationPreference');
    if (locationPreference) {
        locationPreference.addEventListener('change', function() {
            const selected = Array.from(this.selectedOptions).map(option => option.value);
            saveField('locationPreference', JSON.stringify(selected));
        });
    }
    
    // File inputs
    document.querySelectorAll('input[type="file"]').forEach(fileInput => {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const formData = new FormData();
                formData.append('action', 'update_field');
                formData.append('field', this.name);
                formData.append(this.name, this.files[0]);
                
                const draftId = document.querySelector('input[name="draft_id"]')?.value || '';
                if (draftId) {
                    formData.append('draft_id', draftId);
                }
                
                fetch('action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log(`File ${this.name} uploaded successfully`);
                        if (data.portfolio_id && draftId !== data.portfolio_id) {
                            document.querySelector('input[name="draft_id"]').value = data.portfolio_id;
                        }
                    } else {
                        console.error(`Error uploading file ${this.name}:`, data.error);
                    }
                })
                .catch(error => {
                    console.error('Network error uploading file:', error);
                });
            }
        });
    });
}

// Save individual field
function saveField(fieldName, value) {
    const form = document.getElementById('portfolioForm');
    if (!form) return;
    
    const draftId = form.querySelector('input[name="draft_id"]')?.value || '';
    
    const formData = new FormData();
    formData.append('action', 'update_field');
    formData.append('field', fieldName);
    formData.append('value', value);
    formData.append('draft_id', draftId);
    
    fetch('action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update draft ID if provided
            if (data.portfolio_id && draftId !== data.portfolio_id) {
                form.querySelector('input[name="draft_id"]').value = data.portfolio_id;
            }
            console.log(`Field ${fieldName} saved successfully`);
            // Optional: Show success messages if any
            if (data.messages && data.messages.length > 0) {
                console.log('Messages:', data.messages.join(', '));
            }
        } else {
            console.error(`Error saving field ${fieldName}:`, data.error);
        }
    })
    .catch(error => {
        console.error('Network error saving field:', error);
    });
}

// Save entire draft
function saveEntireDraft(buttonEl) {
    const form = document.getElementById('portfolioForm');
    if (!form) return;

    const formData = new FormData(form);
    formData.append('action', 'save_draft');

    const originalHTML = showLoadingSpinner(buttonEl, 'Saving Draft...');
    
    fetch('action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.portfolio_id) {
                const draftIdInput = form.querySelector('input[name="draft_id"]');
                if (draftIdInput) draftIdInput.value = data.portfolio_id;
            }
            showNotificationMessage('Draft saved successfully!', 'success');
        } else {
            showNotificationMessage('Error saving draft: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error saving draft:', error);
        showNotificationMessage('Network error while saving draft: ' + error.message, 'error');
    })
    .finally(() => {
        hideLoadingSpinner(buttonEl, originalHTML);
    });
}

// Form submission handler
function submitPortfolio(e) {
    e.preventDefault();
    
    if (isSubmitting) return;
    
    const form = document.getElementById('portfolioForm');
    if (!form) return;
    
    // NOTE: client-side HTML5 validation bypassed intentionally so submit proceeds
    // even if some inputs are invalid (per request to remove validations).
    
    const formData = new FormData(form);
    formData.append('action', 'submit_portfolio');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHTML = showLoadingSpinner(submitBtn, 'Submitting...');
    isSubmitting = true;
    
    fetch('action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = `add_portfolio.php?success=1&portfolio_id=${data.portfolio_id}`;
        } else {
            showNotificationMessage('Error submitting portfolio: ' + (data.error || 'Unknown error'), 'error');
            hideLoadingSpinner(submitBtn, originalHTML);
            isSubmitting = false;
        }
    })
    .catch(error => {
        console.error('Error submitting portfolio:', error);
        showNotificationMessage('Network error while submitting portfolio: ' + error.message, 'error');
        hideLoadingSpinner(submitBtn, originalHTML);
        isSubmitting = false;
    });
}

// Initialize event listeners
function initializeEventListeners() {
    // Save Draft button click (entire form save)
    document.querySelectorAll('.save-draft').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            saveEntireDraft(this);
        });
    });

    // Form submission handling
    const form = document.getElementById('portfolioForm');
    if (form) {
        form.addEventListener('submit', submitPortfolio);
    }
    
    // Education entries management
    setupEducationEntries();
    
    // Option card selection
    setupOptionCards();
    
    // Name fields handling
    initializeNameFields();
}

// Global function to toggle other inputs
function toggleOther(selectId, containerId, otherValue = 'other') {
    const select = document.getElementById(selectId);
    const container = document.getElementById(containerId);
    if (!select || !container) return;
    
    const input = container.querySelector('input');
    if (select.value === otherValue) {
        container.style.display = 'block';
        if (input) input.required = true;
    } else {
        container.style.display = 'none';
        if (input) {
            input.value = '';
            input.required = false;
        }
    }
}

// Education entries management
function setupEducationEntries() {
    // Add education entry
    const addEducationBtn = document.getElementById('addEducation');
    if (addEducationBtn) {
        addEducationBtn.addEventListener('click', function() {
            const educationEntries = document.getElementById('educationEntries');
            if (!educationEntries) return;
            
            const entries = educationEntries.querySelectorAll('.education-entry');
            const newIndex = entries.length;
            
            const newEntry = entries[0].cloneNode(true);
            
            // Clear all inputs in the new entry
            newEntry.querySelectorAll('input, select').forEach(input => {
                if (input.type !== 'button') {
                    input.value = '';
                    input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                }
            });
            
            // Hide other input container
            const otherContainer = newEntry.querySelector('.degree-other-container');
            if (otherContainer) {
                otherContainer.style.display = 'none';
                const otherInput = otherContainer.querySelector('input');
                if (otherInput) {
                    otherInput.required = false;
                    otherInput.name = otherInput.name.replace(/\[\d+\]/, `[${newIndex}]`);
                }
            }
            
            // Add remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-outline-danger remove-education mt-2';
            removeBtn.textContent = 'Remove Entry';
            newEntry.appendChild(removeBtn);
            
            educationEntries.appendChild(newEntry);
        });
    }

    // Remove education entry (delegated event)
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-education')) {
            const educationEntries = document.getElementById('educationEntries');
            if (!educationEntries) return;
            
            if (educationEntries.children.length > 1) {
                e.target.closest('.education-entry').remove();
            } else {
                showNotificationMessage('At least one education entry is required', 'info');
            }
        }
    });

    // Degree selection with "Other" option
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('degree-select')) {
            const container = e.target.closest('.education-entry').querySelector('.degree-other-container');
            if (!container) return;
            
            if (e.target.value === 'Others') {
                container.style.display = 'block';
                const input = container.querySelector('input');
                if (input) input.required = true;
            } else {
                container.style.display = 'none';
                const input = container.querySelector('input');
                if (input) {
                    input.value = '';
                    input.required = false;
                }
            }
            saveField('degree[]', e.target.value);
        }
    });
}

function initializeEducationOtherInputs() {
    document.querySelectorAll('.education-entry').forEach(entry => {
        const select = entry.querySelector('.degree-select');
        const container = entry.querySelector('.degree-other-container');
        if (!select || !container) return;
        if (select.value === 'Others') {
            container.style.display = 'block';
            const input = container.querySelector('input');
            if (input) input.required = true;
        } else {
            container.style.display = 'none';
            const input = container.querySelector('input');
            if (input) input.required = false;
        }
    });
}

// Option card selection
function setupOptionCards() {
    document.querySelectorAll('.option-card').forEach(card => {
        card.addEventListener('click', function() {
            const radioInput = this.querySelector('input[type="radio"]');
            if (radioInput) {
                radioInput.checked = true;
                
                // Update visual selection
                document.querySelectorAll('.option-card').forEach(c => {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');
                
                saveField('profileType', radioInput.value);
            }
        });
    });

    // Initialize selected state for option cards
    document.querySelectorAll('.option-card input[type="radio"]').forEach(radio => {
        if (radio.checked) {
            radio.closest('.option-card').classList.add('selected');
        }
    });
}

// Notification system
function showNotificationMessage(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notificationArea = document.getElementById('formNotificationArea');
    if (!notificationArea) return;
    
    const notification = document.createElement('div');
    notification.className = `custom-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    notificationArea.innerHTML = '';
    notificationArea.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Loading spinner management
function showLoadingSpinner(button, text = 'Saving...') {
    const originalHTML = button.innerHTML;
    button.innerHTML = `<i class="fa fa-spinner fa-spin me-2"></i> ${text}`;
    button.disabled = true;
    button.classList.add('saving');
    return originalHTML;
}

function hideLoadingSpinner(button, originalHTML) {
    button.innerHTML = originalHTML;
    button.disabled = false;
    button.classList.remove('saving');
}