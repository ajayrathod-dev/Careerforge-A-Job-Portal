<?php
// portfolio_form_fields.php
// This file contains the form fields for both add and update portfolio pages

// $portfolio variable will contain either the draft (for add) or existing portfolio (for update)
?>

<!-- Profile Type Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-user-tie me-2"></i>Profile Type
    </h4>
    
    <div class="option-card">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="profileType" id="profileType1" value="1" <?php if (isset($portfolio['profile_type']) && $portfolio['profile_type'] == '1') echo 'checked'; ?> required>
            <label class="form-check-label" for="profileType1">
                Candidates currently in the US and looking for jobs in India
            </label>
        </div>
    </div>

    <div class="option-card">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="profileType" id="profileType2" value="2" <?php if (isset($portfolio['profile_type']) && $portfolio['profile_type'] == '2') echo 'checked'; ?> required>
            <label class="form-check-label" for="profileType2">
                Candidates with US work experience currently in India and looking for jobs in India
            </label>
        </div>
    </div>

    <div class="option-card">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="profileType" id="profileType3" value="3" <?php if (isset($portfolio['profile_type']) && $portfolio['profile_type'] == '3') echo 'checked'; ?> required>
            <label class="form-check-label" for="profileType3">
                Candidates currently pursuing studies in the US and looking for jobs in India
            </label>
        </div>
    </div>
</div>

<!-- Personal Information Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-user me-2"></i>Personal Information
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fullName" class="form-label"><i class="fas fa-user"></i> Full Name *</label>
                <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($portfolio['full_name'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email *</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($portfolio['email'] ?? ''); ?>" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="countryCode" class="form-label"><i class="fas fa-phone"></i> Country Code *</label>
                <select class="form-select" id="countryCode" name="countryCode" required onchange="toggleOther('countryCode', 'countryCodeOtherContainer', 'other')">
                    <option value="">Select Code</option>
                    <option value="+1" <?php if (isset($portfolio['country_code']) && $portfolio['country_code'] == '+1') echo 'selected'; ?>>+1 (USA/Canada)</option>
                    <option value="+91" <?php if (isset($portfolio['country_code']) && $portfolio['country_code'] == '+91') echo 'selected'; ?>>+91 (India)</option>
                    <option value="+44" <?php if (isset($portfolio['country_code']) && $portfolio['country_code'] == '+44') echo 'selected'; ?>>+44 (UK)</option>
                    <option value="+61" <?php if (isset($portfolio['country_code']) && $portfolio['country_code'] == '+61') echo 'selected'; ?>>+61 (Australia)</option>
                    <option value="+49" <?php if (isset($portfolio['country_code']) && $portfolio['country_code'] == '+49') echo 'selected'; ?>>+49 (Germany)</option>
                    <option value="other" <?php if (isset($portfolio['country_code']) && !in_array($portfolio['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo 'selected'; ?>>Other</option>
                </select>
                <div id="countryCodeOtherContainer" class="other-input-container" style="<?php if (isset($portfolio['country_code']) && !in_array($portfolio['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                    <label for="countryCode_other" class="other-input-label">Specify Country Code</label>
                    <input type="text" class="form-control" id="countryCode_other" name="countryCode_other" value="<?php if (isset($portfolio['country_code']) && !in_array($portfolio['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo htmlspecialchars($portfolio['country_code']); ?>" placeholder="e.g., +33">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number *</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($portfolio['phone_number'] ?? ''); ?>" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="currentLocation" class="form-label"><i class="fas fa-map-marker-alt"></i> Current Location *</label>
                <input type="text" class="form-control" id="currentLocation" name="currentLocation" value="<?php echo htmlspecialchars($portfolio['current_location'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="linkedin" class="form-label"><i class="fab fa-linkedin"></i> LinkedIn Profile</label>
                <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($portfolio['linkedin_profile'] ?? ''); ?>">
            </div>
        </div>
    </div>
</div>

<!-- Professional Experience Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-briefcase me-2"></i>Professional Experience
    </h4>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="totalExperience" class="form-label">Total Experience (Years) *</label>
                <input type="number" step="0.5" class="form-control" id="totalExperience" name="totalExperience" value="<?php echo htmlspecialchars($portfolio['total_experience'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="currentDesignation" class="form-label">Current Designation *</label>
                <input type="text" class="form-control" id="currentDesignation" name="currentDesignation" value="<?php echo htmlspecialchars($portfolio['current_designation'] ?? ''); ?>" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="technicalStrengths" class="form-label">Technical Strengths *</label>
                <textarea class="form-control" id="technicalStrengths" name="technicalStrengths" rows="3" required><?php echo htmlspecialchars($portfolio['technical_strengths'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="personalStrengths" class="form-label">Personal Strengths *</label>
                <textarea class="form-control" id="personalStrengths" name="personalStrengths" rows="3" required><?php echo htmlspecialchars($portfolio['personal_strengths'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Project Management Section -->
<div class="form-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-tasks me-2"></i>Project Management Experience
    </h4>
    <div class="mb-4">
        <label class="form-label"><i class="fas fa-tasks"></i> Do you have Project Management experience? *</label>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input type="radio" name="project_management_exp" value="yes" class="form-check-input" <?php echo (($portfolio['project_management_exp'] ?? '') == 'yes') ? 'checked' : ''; ?> required>
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input type="radio" name="project_management_exp" value="no" class="form-check-input" <?php echo (($portfolio['project_management_exp'] ?? 'no') == 'no' || empty($portfolio['project_management_exp'])) ? 'checked' : ''; ?> required>
                <label class="form-check-label">No</label>
            </div>
        </div>
        <div id="project-management-details" class="mt-3 <?php echo (($portfolio['project_management_exp'] ?? '') != 'yes') ? 'd-none' : ''; ?>">
            <div class="mb-3">
                <label for="project_management_skills" class="form-label">Add Project Management Skills</label>
                <textarea name="project_management_skills" rows="3" class="form-control" id="project_management_skills" placeholder="Describe your project management skills and experience"><?php echo htmlspecialchars($portfolio['project_management_skills'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Cross Industry Experience Section -->
<div class="form-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-exchange-alt me-2"></i>Cross Industry Experience
    </h4>
    <div class="mb-4">
        <label class="form-label"><i class="fas fa-exchange-alt"></i> Are you eligible/willing for cross-industry experience? *</label>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input type="radio" name="cross_industry_exp" value="yes" class="form-check-input" <?php echo (($portfolio['cross_industry_exp'] ?? '') == 'yes') ? 'checked' : ''; ?> required>
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input type="radio" name="cross_industry_exp" value="no" class="form-check-input" <?php echo (($portfolio['cross_industry_exp'] ?? 'no') == 'no' || empty($portfolio['cross_industry_exp'])) ? 'checked' : ''; ?> required>
                <label class="form-check-label">No</label>
            </div>
        </div>
    </div>
</div>

<!-- US Experience Section -->
<div class="form-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-flag-usa me-2"></i>US Experience
    </h4>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label"><i class="fas fa-flag-usa"></i> US Experience</label>
        <button type="button" id="add-us-experience" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add More
        </button>
    </div>
    <div id="us-experience-container">
        <?php if (!empty($portfolio['us_experience'])): ?>
            <?php foreach($portfolio['us_experience'] as $index => $exp): ?>
                <div class="experience-block" data-index="<?php echo $index; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="form-label mb-0">Experience #<?php echo $index + 1; ?></h6>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'us')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Name of Employer</label>
                            <input type="text" name="us_experience[<?php echo $index; ?>][employer]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['employer'] ?? ''); ?>" placeholder="Employer name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                            <div class="d-flex gap-2">
                                <input type="text" name="us_experience[<?php echo $index; ?>][from]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['from'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                <span class="align-self-center">to</span>
                                <input type="text" name="us_experience[<?php echo $index; ?>][to]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['to'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" name="us_experience[<?php echo $index; ?>][state]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['state'] ?? ''); ?>" placeholder="State">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="us_experience[<?php echo $index; ?>][city]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['city'] ?? ''); ?>" placeholder="City">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="experience-block" data-index="0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="form-label mb-0">Experience #1</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'us')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Name of Employer</label>
                        <input type="text" name="us_experience[0][employer]" class="form-control experience-input" placeholder="Employer name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                        <div class="d-flex gap-2">
                            <input type="text" name="us_experience[0][from]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                            <span class="align-self-center">to</span>
                            <input type="text" name="us_experience[0][to]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="us_experience[0][state]" class="form-control experience-input" placeholder="State">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="us_experience[0][city]" class="form-control experience-input" placeholder="City">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- India Experience Section -->
<div class="form-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-flag me-2"></i>India Experience
    </h4>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label"><i class="fas fa-flag"></i> India Experience (If any)</label>
        <button type="button" id="add-india-experience" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add More
        </button>
    </div>
    <div id="india-experience-container">
        <?php if (!empty($portfolio['india_experience'])): ?>
            <?php foreach($portfolio['india_experience'] as $index => $exp): ?>
                <div class="experience-block" data-index="<?php echo $index; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="form-label mb-0">Experience #<?php echo $index + 1; ?></h6>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'india')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Name of Employer</label>
                            <input type="text" name="india_experience[<?php echo $index; ?>][employer]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['employer'] ?? ''); ?>" placeholder="Employer name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                            <div class="d-flex gap-2">
                                <input type="text" name="india_experience[<?php echo $index; ?>][from]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['from'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                <span class="align-self-center">to</span>
                                <input type="text" name="india_experience[<?php echo $index; ?>][to]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['to'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" name="india_experience[<?php echo $index; ?>][state]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['state'] ?? ''); ?>" placeholder="State">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="india_experience[<?php echo $index; ?>][city]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['city'] ?? ''); ?>" placeholder="City">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="experience-block" data-index="0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="form-label mb-0">Experience #1</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'india')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Name of Employer</label>
                        <input type="text" name="india_experience[0][employer]" class="form-control experience-input" placeholder="Employer name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                        <div class="d-flex gap-2">
                            <input type="text" name="india_experience[0][from]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                            <span class="align-self-center">to</span>
                            <input type="text" name="india_experience[0][to]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="india_experience[0][state]" class="form-control experience-input" placeholder="State">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="india_experience[0][city]" class="form-control experience-input" placeholder="City">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Basic Professional Information -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-briefcase me-2"></i>Basic Professional Information
    </h4>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="experience" class="form-label"><i class="fas fa-briefcase"></i> Total Full-time Work Experience (Years) *</label>
            <input type="number" class="form-control" id="experience" name="experience" min="0" step="0.1" value="<?php echo htmlspecialchars($portfolio['total_experience'] ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="currentDesignation" class="form-label"><i class="fas fa-id-badge"></i> Current / Latest Designation *</label>
            <input type="text" class="form-control" id="currentDesignation" name="currentDesignation" value="<?php echo htmlspecialchars($portfolio['current_designation'] ?? ''); ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="technicalStrengths" class="form-label"><i class="fas fa-tools"></i> Technical Strengths *</label>
        <textarea class="form-control" id="technicalStrengths" name="technicalStrengths" rows="3" required><?php echo htmlspecialchars($portfolio['technical_strengths'] ?? ''); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="personalStrengths" class="form-label"><i class="fas fa-user-check"></i> Personal Strengths *</label>
        <textarea class="form-control" id="personalStrengths" name="personalStrengths" rows="3" required><?php echo htmlspecialchars($portfolio['personal_strengths'] ?? ''); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="functionsDesired" class="form-label"><i class="fas fa-tasks"></i> Functions Desired *</label>
        <select class="form-select" id="functionsDesired" name="functionsDesired" required onchange="toggleOther('functionsDesired', 'functionsDesiredOtherContainer', 'other')">
            <option value="">Select Function</option>
            <option value="Individual contributor" <?php if (isset($portfolio['functions_desired']) && $portfolio['functions_desired'] == 'Individual contributor') echo 'selected'; ?>>Individual contributor</option>
            <option value="Team member" <?php if (isset($portfolio['functions_desired']) && $portfolio['functions_desired'] == 'Team member') echo 'selected'; ?>>Team member</option>
            <option value="Project manager" <?php if (isset($portfolio['functions_desired']) && $portfolio['functions_desired'] == 'Project manager') echo 'selected'; ?>>Project manager</option>
            <option value="Leadership role" <?php if (isset($portfolio['functions_desired']) && $portfolio['functions_desired'] == 'Leadership role') echo 'selected'; ?>>Leadership role</option>
            <option value="other" <?php if (isset($portfolio['functions_desired']) && !in_array($portfolio['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo 'selected'; ?>>Other (mention)</option>
        </select>
        <div id="functionsDesiredOtherContainer" class="other-input-container" style="<?php if (isset($portfolio['functions_desired']) && !in_array($portfolio['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
            <label for="functionsDesired_other" class="other-input-label">Mention</label>
            <input type="text" class="form-control" id="functionsDesired_other" name="functionsDesired_other" value="<?php if (isset($portfolio['functions_desired']) && !in_array($portfolio['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo htmlspecialchars($portfolio['functions_desired']); ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="currentSalary" class="form-label"><i class="fas fa-dollar-sign"></i> Current / Latest Annual Salary *</label>
            <input type="number" class="form-control" id="currentSalary" name="currentSalary" value="<?php echo htmlspecialchars($portfolio['current_salary'] ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="salaryCurrency" class="form-label">Currency *</label>
            <select class="form-select" id="salaryCurrency" name="salaryCurrency" required>
                <option value="">Select Currency</option>
                <option value="USD" <?php if (isset($portfolio['salary_currency']) && $portfolio['salary_currency'] == 'USD') echo 'selected'; ?>>USD</option>
                <option value="INR" <?php if (isset($portfolio['salary_currency']) && $portfolio['salary_currency'] == 'INR') echo 'selected'; ?>>INR</option>
            </select>
        </div>
    </div>
</div>

<!-- Job Preferences Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-map me-2"></i>Job Preferences
    </h4>

    <div class="mb-3">
        <label for="locationPreference" class="form-label"><i class="fas fa-map"></i> Location Preference in India *</label>
        <select class="form-select" id="locationPreference" name="locationPreference[]" multiple required>
            <option value="No preference" <?php if (in_array('No preference', $portfolio['location_preference'])) echo 'selected'; ?>>No preference</option>
            <option value="Andhra Pradesh" <?php if (in_array('Andhra Pradesh', $portfolio['location_preference'])) echo 'selected'; ?>>Andhra Pradesh</option>
            <option value="Karnataka" <?php if (in_array('Karnataka', $portfolio['location_preference'])) echo 'selected'; ?>>Karnataka</option>
            <option value="Tamil Nadu" <?php if (in_array('Tamil Nadu', $portfolio['location_preference'])) echo 'selected'; ?>>Tamil Nadu</option>
            <option value="Telangana" <?php if (in_array('Telangana', $portfolio['location_preference'])) echo 'selected'; ?>>Telangana</option>
            <option value="Maharashtra" <?php if (in_array('Maharashtra', $portfolio['location_preference'])) echo 'selected'; ?>>Maharashtra</option>
        </select>
        <div class="form-text">Hold Ctrl/Cmd to select multiple locations</div>
    </div>

    <div class="mb-3">
        <label for="relocate" class="form-label"><i class="fas fa-truck-moving"></i> Willing to Relocate within India? *</label>
        <select class="form-select" id="relocate" name="relocate" required>
            <option value="">Select</option>
            <option value="Yes" <?php if (isset($portfolio['willing_relocate']) && $portfolio['willing_relocate'] == 'Yes') echo 'selected'; ?>>Yes</option>
            <option value="No" <?php if (isset($portfolio['willing_relocate']) && $portfolio['willing_relocate'] == 'No') echo 'selected'; ?>>No</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="availability" class="form-label"><i class="fas fa-calendar-check"></i> Availability to Join in India *</label>
        <select class="form-select" id="availability" name="availability" required>
            <option value="">Select</option>
            <option value="Immediate" <?php if (isset($portfolio['availability']) && $portfolio['availability'] == 'Immediate') echo 'selected'; ?>>Immediate</option>
            <option value="Within 2 weeks" <?php if (isset($portfolio['availability']) && $portfolio['availability'] == 'Within 2 weeks') echo 'selected'; ?>>Within 2 weeks</option>
            <option value="Within 1 month" <?php if (isset($portfolio['availability']) && $portfolio['availability'] == 'Within 1 month') echo 'selected'; ?>>Within 1 month</option>
            <option value="Within 2 months" <?php if (isset($portfolio['availability']) && $portfolio['availability'] == 'Within 2 months') echo 'selected'; ?>>Within 2 months</option>
            <option value="Within 3 months" <?php if (isset($portfolio['availability']) && $portfolio['availability'] == 'Within 3 months') echo 'selected'; ?>>Within 3 months</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="languages" class="form-label"><i class="fas fa-language"></i> Languages Spoken *</label>
        <input type="text" class="form-control" id="languages" name="languages" value="<?php echo htmlspecialchars($portfolio['languages_spoken'] ?? ''); ?>" required>
        <div class="form-text">Include Indian regional languages</div>
    </div>
</div>

<!-- Documents Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-file-alt me-2"></i>Documents
    </h4>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="resume" class="form-label">Resume (PDF/DOC/DOCX) *</label>
                <?php if (!empty($portfolio['resume_path'])): ?>
                <div class="current-file mb-2">
                    Current file: <?php echo basename($portfolio['resume_path']); ?>
                </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" <?php echo empty($portfolio['resume_path']) ? 'required' : ''; ?>>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="portfolio" class="form-label">Portfolio (PDF/DOC/DOCX)</label>
                <?php if (!empty($portfolio['portfolio_path'])): ?>
                <div class="current-file mb-2">
                    Current file: <?php echo basename($portfolio['portfolio_path']); ?>
                </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="portfolio" name="portfolio" accept=".pdf,.doc,.docx">
            </div>
        </div>
    </div>
</div>

<!-- Education Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-graduation-cap me-2"></i>Educational Background
    </h4>
    
    <div id="educationEntries">
        <?php if (!empty($portfolio['education'])): ?>
            <?php foreach ($portfolio['education'] as $index => $edu): ?>
                <div class="education-entry">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Degree</label>
                            <select class="form-select degree-select" name="degree[]" onchange="toggleDegreeOther(this)">
                                <option value="">Select Degree</option>
                                <option value="Bachelor's" <?php if ($edu['degree_type'] == "Bachelor's") echo 'selected'; ?>>Bachelor's</option>
                                <option value="Master's" <?php if ($edu['degree_type'] == "Master's") echo 'selected'; ?>>Master's</option>
                                <option value="PhD" <?php if ($edu['degree_type'] == 'PhD') echo 'selected'; ?>>PhD</option>
                                <option value="MBA" <?php if ($edu['degree_type'] == 'MBA') echo 'selected'; ?>>MBA</option>
                                <option value="Others" <?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo 'selected'; ?>>Others</option>
                            </select>
                            <div class="degree-other-container other-input-container" style="<?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                <label class="other-input-label">Specify Other Degree</label>
                                <input type="text" class="form-control" name="degree_other[]" value="<?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo htmlspecialchars($edu['degree_type']); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Year of Graduation</label>
                            <input type="number" class="form-control" name="gradYear[]" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($edu['graduation_year']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">University Name</label>
                            <input type="text" class="form-control" name="university[]" value="<?php echo htmlspecialchars($edu['university_name']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" class="form-control" name="specialization[]" value="<?php echo htmlspecialchars($edu['specialization']); ?>">
                        </div>
                    </div>
                    <?php if ($index > 0): ?>
                        <button type="button" class="btn btn-outline-danger remove-education mt-2">Remove Entry</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="education-entry">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Degree</label>
                        <select class="form-select degree-select" name="degree[]" onchange="toggleDegreeOther(this)">
                            <option value="">Select Degree</option>
                            <option value="Bachelor's">Bachelor's</option>
                            <option value="Master's">Master's</option>
                            <option value="PhD">PhD</option>
                            <option value="MBA">MBA</option>
                            <option value="Others">Others</option>
                        </select>
                        <div class="degree-other-container other-input-container" style="display: none;">
                            <label class="other-input-label">Specify Other Degree</label>
                            <input type="text" class="form-control" name="degree_other[]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year of Graduation</label>
                        <input type="number" class="form-control" name="gradYear[]" min="1900" max="<?php echo date('Y'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">University Name</label>
                        <input type="text" class="form-control" name="university[]">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Specialization</label>
                        <input type="text" class="form-control" name="specialization[]">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button type="button" id="addEducation" class="btn btn-outline-primary mb-3"><i class="fas fa-plus me-2"></i>Add Another Education Entry</button>
</div>

<!-- Additional Information Section -->
<div class="form-section">
    <h4 class="section-title">
        <i class="fas fa-info-circle me-2"></i>Additional Information
    </h4>
    
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="additionalInfo" class="form-label">Additional Information</label>
                <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3"><?php echo htmlspecialchars($portfolio['additional_info'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>