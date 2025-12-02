<?php
/**
 * Estilos CSS reutilizables para todos los CRUDs
 * Incluir este archivo en cada CRUD para mantener consistencia
 */
?>
<style>
/* Estilos comunes para CRUDs */
.crud-header-card {
    background: linear-gradient(135deg, rgba(91, 155, 213, 0.1), rgba(91, 155, 213, 0.05));
    border-left: 4px solid #5b9bd5;
}

.crud-card-mobile {
    transition: all 0.3s ease;
    border-left: 4px solid #5b9bd5;
    overflow: hidden;
}

.crud-card-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(91, 155, 213, 0.2) !important;
}

.crud-card-mobile .card-body {
    overflow: hidden;
    word-wrap: break-word;
}

.crud-card-mobile .badge {
    white-space: nowrap;
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.crud-table tbody tr {
    transition: all 0.2s ease;
}

.crud-table tbody tr:hover {
    background-color: rgba(91, 155, 213, 0.05);
    transform: scale(1.01);
}

.crud-table thead th {
    background: linear-gradient(180deg, var(--bg-surface-1), var(--bg-surface-2));
    color: #5b9bd5;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(91, 155, 213, 0.2);
}

/* Mejoras móviles */
@media (max-width: 767.98px) {
    .crud-card-mobile .btn {
        min-height: 44px;
        font-size: 0.95rem;
    }
    
    .crud-card-mobile .card-body {
        padding: 1rem !important;
    }
    
    .crud-card-mobile .d-flex {
        flex-wrap: wrap;
    }
    
    .crud-card-mobile .text-end {
        width: 100%;
        text-align: left !important;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .crud-card-mobile .text-end .badge {
        display: inline-block;
        margin-bottom: 0.25rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .form-control, .form-select {
        min-height: 48px;
        font-size: 16px;
    }
}

/* Avatares para usuarios/alumnos */
.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #5b9bd5, #4a8bc2);
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.avatar-initials {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Animaciones */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.crud-card-mobile {
    animation: fadeIn 0.3s ease;
}

.crud-table tbody tr {
    animation: fadeIn 0.3s ease;
}
</style>

