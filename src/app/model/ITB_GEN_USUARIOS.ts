interface ITB_GEN_USUARIOS {
    id_usuario?: number;
    id_perfil?: number;
    login?: string;
    clave?: string;
    nombre?: string;
    apellido?: string;
    email?: string;
    estado_usuario?: string;
    fecha_ingreso?: string;
    id_usuario_ingreso?: number;
    fecha_actualizacion?: string;
    id_usuario_actualizacion?: number;
    fecha_anulacion?: string;
    id_usuario_anulacion?: number;
}

export default ITB_GEN_USUARIOS;