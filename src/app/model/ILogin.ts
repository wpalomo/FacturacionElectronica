interface ILogin {
    id?: number;
    id_perfil?: number;
    descripcion?: string;
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
