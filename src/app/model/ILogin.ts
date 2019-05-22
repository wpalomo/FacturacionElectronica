interface ILogin {
    id_usuario?: number;
    id_perfil?: number;
    descripcion_perfil?: string;
    login?: string;
    nombre?: string;
    apellido?: string;
    nombre_apellido?: string;
    apellido_nombre?: string;
    email?: string;
    estado_usuario?: string;
    descripcio_estado_usuario?: string;
}

export default ILogin;
