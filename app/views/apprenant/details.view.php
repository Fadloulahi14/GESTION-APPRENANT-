<!-- Titre de la page -->
<div class="apprenant-header">
    <h1 class="app-title">Apprenants</h1>
    <span class="page-title">/ Détails</span>
</div>

<!-- Contenu principal des détails de l'apprenant -->
<div class="apprenant-content-wrapper">
    <!-- Profil et infos de contact -->
    <div class="apprenant-profile">
        <a href="?page=liste_apprenant" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"></path>
            </svg>
            Retour sur la liste
        </a>
        
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?= !empty($apprenant['photo']) ? htmlspecialchars($apprenant['photo']) : 'assets/images/default-avatar.png' ?>" alt="<?= htmlspecialchars($apprenant['nom_complet']) ?>">
            </div>
            <h2 class="profile-name"><?= htmlspecialchars($apprenant['nom_complet']) ?></h2>
            <div class="profile-role">
                <?php
                $refName = '';
                foreach ($referenciels as $ref) {
                    if (($ref['id'] ?? 0) == ($apprenant['referenciel'] ?? 0)) {
                        $refName = $ref['nom'];
                        break;
                    }
                }
                echo htmlspecialchars($refName ?: 'DEV WEB/MOBILE');
                ?>
            </div>
            <div class="profile-status"><?= ucfirst($apprenant['statut'] ?? 'Retenu') ?></div>
        </div>




        
        <div class="contact-info">
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span><?= htmlspecialchars($apprenant['telephone'] ?? 'Non renseigné') ?></span>
            </div>



            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span><?= htmlspecialchars($apprenant['login'] ?? 'Non renseigné') ?></span>
            </div>



            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span><?= htmlspecialchars($apprenant['adresse'] ?? 'Non renseigné') ?></span>
            </div>
        </div>
    </div>






    
    <!-- Statistiques et modules -->
    <div class="apprenant-details">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon presence-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <path d="M9 12l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">40</div>
                    <div class="stat-label">Présence(s)</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon retard-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">7</div>
                    <div class="stat-label">Retard(s)</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon absence-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Absence(s)</div>
                </div>
            </div>
        </div>









        
        <div class="tabs-header">
            <div class="tab-title">Programme & Modules</div>
            <div class="absences-info">
                Total absences par étudiant
            </div>
        </div>




        
        <div class="modules-grid">
            <!-- Module 1 -->
            <div class="module-card colored-border algo-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        30 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Algorithme & Langage C</h3>
                    <p class="module-description">Complexité algorithmique & pratique codage en langage C</p>
                    <div class="module-status completed">Débutant</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        15 Février 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>



            
            <!-- Module 2 -->
            <div class="module-card colored-border frontend-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        15 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Frontend 1: Html, Css & JS</h3>
                    <p class="module-description">Création d'interfaces de design avec animations avancées !</p>
                    <div class="module-status completed">Débutant</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        24 Mars 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>



            
            <!-- Module 3 -->
            <div class="module-card colored-border backend-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        20 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Backend 1: PHP & MySQL</h3>
                    <p class="module-description">Développement backend avec PHP et bases de données MySQL</p>
                    <div class="module-status ongoing">En cours</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        10 Avril 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>

            <!-- Module 4 -->
            <div class="module-card colored-border algo-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        25 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Framework JavaScript</h3>
                    <p class="module-description">Développement d'applications avec React et Vue.js</p>
                    <div class="module-status">À venir</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        5 Mai 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>

            <!-- Module 5 -->
            <div class="module-card colored-border frontend-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        18 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Développement Mobile</h3>
                    <p class="module-description">Création d'applications mobiles avec React Native</p>
                    <div class="module-status">À venir</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        30 Mai 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>

            <!-- Module 6 -->
            <div class="module-card colored-border backend-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        22 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Projet Final</h3>
                    <p class="module-description">Développement d'un projet complet en équipe</p>
                    <div class="module-status">À venir</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        15 Juin 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>
        </div>
    </div>












</div>


















































<style>
    /* Reset et styles de base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f1f5f9;
    font-family: Arial, sans-serif;
}

/* Container principal */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    margin-top:  5%;
}

/* Style des cartes */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
}

/* En-tête profil */
.profile-card {
    display: flex;
    align-items: center;
    gap: 20px;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-info {
    flex-grow: 1;
}

.profile-name {
    font-size: 24px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 5px;
}

.profile-job {
    color: #ff7900;
    margin-bottom: 10px;
}

.profile-contact {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.contact-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748b;
}

/* Grille des statistiques */
.card-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

/* Titre des cartes */
.card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: #1e293b;
    font-weight: 500;
}

.orange-icon {
    color: #ff7900;
}

/* Stats de présence */
.presence-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    border-radius: 8px;
}

.present-bg { background: #e8f5e9; }
.retard-bg { background: #fff3e0; }
.absent-bg { background: #ffebee; }

.stat-number {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 5px;
}

.present { color: #2e7d32; }
.retard { color: #f57c00; }
.absent { color: #d32f2f; }

/* QR Code section */
.qr-section {
    text-align: center;
}

.qr-title {
    font-weight: 500;
    margin-bottom: 15px;
    color: #1e293b;
}

.qr-code {
    width: 150px;
    height: 150px;
    margin: 15px 0;
}

.qr-subtitle {
    color: #64748b;
    margin-bottom: 5px;
}

.qr-id {
    color: #ff7900;
    font-weight: 500;
}

/* Historique et recherche */
.search-filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.search-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.filter-button {
    padding: 8px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    color: #64748b;
    cursor: pointer;
}

/* Table d'historique */
.presence-history table {
    width: 100%;
    border-collapse: collapse;
}

.presence-history th,
.presence-history td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.presence-history th {
    color: #64748b;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 12px;
    font-weight: 500;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.pagination-info {
    color: #64748b;
}

.pagination-controls {
    display: flex;
    gap: 5px;
}

.pagination-button {
    padding: 6px 12px;
    border: none;
    background: none;
    color: #64748b;
    cursor: pointer;
}

.pagination-button.active {
    background: #ff7900;
    color: white;
    border-radius: 4px;
}

/* Header styles */
.app-header {
    background: white;
    padding: 0.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 0;
}

.header-left .logo {
    height: 30px;
    width: auto;
}

.header-right .user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-profile .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f1f5f9;
}

/* Profile Card Styles */
.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.profile-main {
    display: flex;
    gap: 20px;
    width: 100%;
}

.profile-image-container {
    position: relative;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #ff7900;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 24px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}

.profile-job {
    color: #ff7900;
    font-size: 16px;
    margin-bottom: 16px;
}

.profile-details {
    display: flex;
    gap: 20px;
    color: #64748b;
}

.profile-detail {
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-detail i {
    color: #94a3b8;
}

@media (max-width: 768px) {
    .app-header {
        padding: 0.5rem 1rem;
    }

    .card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
    background color: #fff;
    margin-left: 0;
    margin-bottom: 20px;
    margin-right: 10%;
}

    .profile-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 15px;
    }

    .profile-main {
        flex-direction: column;
        align-items: center;
    }

    .profile-image {
        width: 100px;
        height: 100px;
    }

    .profile-details {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}

/* Navigation links */
.nav-menu {
    display: flex;
    gap: 20px;
}

.nav-links {
    display: flex;
    gap: 20px;
}

.nav-link {
    text-decoration: none;
    color: #6b7280;
    font-size: 14px;
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

/* Style spécifique pour le bouton Mon Profil */
.nav-link[href*="profile"] {
    color: #4f46e5;
    background-color: #eef2ff;
}

.nav-link[href*="profile"]:hover {
    background-color: #e0e7ff;
    color: #4338ca;
}

/* Style spécifique pour le bouton Déconnexion */
.nav-link[href*="logout"] {
    color: #dc2626;
    background-color: #fef2f2;
}

.nav-link[href*="logout"]:hover {
    background-color: #fee2e2;
    color: #b91c1c;
}

/* Styles des onglets */
.card-tabs {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.tab-btn {
    padding: 0.5rem 1rem;
    border: none;
    background: none;
    color: #64748b;
    cursor: pointer;
    font-weight: 500;
    position: relative;
}

.tab-btn.active {
    color: #ff7900;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #ff7900;
}

.tab-content {
    display: block;
}

.tab-content.hidden {
    display: none;
}

/* Style pour la liste des informations manquantes */
.missing-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.missing-info li {
    color: #ef4444;
    font-size: 0.875rem;
    padding: 2px 0;
}


/* Styles spécifiques à la page détails apprenant, qui ne devraient pas entrer en conflit avec votre layout */
.apprenant-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 10px;
}
.app-title {
    font-size: 24px;
    font-weight: 500;
    color: #17a2b8;
    margin-right: 5px;
}
.page-title {
    font-size: 18px;
    color: #fd7e14;
    font-weight: 400;
}
.apprenant-content-wrapper {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 20px;
}
.apprenant-profile {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    padding: 20px;
}
.back-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}
.back-link svg {
    margin-right: 5px;
}
.profile-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 20px;
}
.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 15px;
    border: 3px solid #f1f1f1;
}
.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-name {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.profile-role {
    display: inline-block;
    background-color: #20c997;
    color: white;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-bottom: 10px;
}
.profile-status {
    background-color: #e3fcef;
    color: #20c997;
    padding: 3px 15px;
    border-radius: 20px;
    font-size: 12px;
}
.contact-info {
    margin-top: 20px;
}
.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14px;
    color: #555;
}
.contact-item svg {
    margin-right: 10px;
    min-width: 20px;
}
.contact-item span {
    word-break: break-word;
}

/* Styles pour la partie droite avec les stats et modules */
.apprenant-details {
    display: flex;
    flex-direction: column;
}
.stats-row {
    display: flex;
    margin-bottom: 20px;
    gap: 15px;
}
.stat-card {
    flex: 1;
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}
.presence-icon {
    background-color: #c2e7de;
    color: #20c997;
}
.retard-icon {
    background-color: #fff3cd;
    color: #fd7e14;
}
.absence-icon {
    background-color: #f8d7da;
    color: #dc3545;
}
.stat-icon svg {
    width: 24px;
    height: 24px;
}
.stat-content {
    display: flex;
    flex-direction: column;
}
.stat-value {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    line-height: 1;
}
.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}
.tabs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.tab-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    background-color: #fd7e14;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
}
.absences-info {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 10px 15px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.module-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    overflow: hidden;
}
.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #eaeaea;
}
.module-duration {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #555;
}
.module-duration svg {
    margin-right: 5px;
}
.module-actions {
    display: flex;
    align-items: center;
}
.module-action {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    border-radius: 4px;
    margin-left: 5px;
    cursor: pointer;
}
.module-content {
    padding: 15px;
}
.module-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.module-description {
    font-size: 13px;
    color: #666;
    margin-bottom: 15px;
}
.module-status {
    display: inline-block;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 15px;
    margin-bottom: 15px;
}
.module-status.completed {
    background-color: #e3fcef;
    color: #20c997;
}
.module-status.ongoing {
    background-color: #e2f5ff;
    color: #17a2b8;
}
.module-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-top: 1px solid #eaeaea;
}
.module-date {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #666;
}
.module-date svg {
    margin-right: 5px;
}
.module-time {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #666;
}
.module-time svg {
    margin-right: 5px;
    color: #fd7e14;
}
.colored-border {
    border-top: 3px solid;
    margin-top: -3px;
}
.algo-border {
    border-color: #343a40;
}
.frontend-border {
    border-color: #28a745;
}
.backend-border {
    border-color: #007bff;
}

/* Responsive fixes */
@media (max-width: 992px) {
    .apprenant-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .apprenant-profile {
        order: 1;
    }
    
    .apprenant-details {
        order: 0;
    }
}

@media (max-width: 768px) {
    .stats-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .modules-grid {
        grid-template-columns: 1fr;
    }
}
</style>