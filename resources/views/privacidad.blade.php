<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel SB Admin 2">
    <meta name="author" content="Alejandro RH">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Shopping Oriental - Politica y Provacidad</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">

     {{-- datatables --}}
     <link href=" https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
     <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.jqueryui.min.css" rel="stylesheet">

     {{-- estilo de vista arbol --}}
     <link rel="stylesheet" href="{{ asset('vista_arbol/themes/default/style.min.css') }}"/>
     <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}"/>
     <style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
     @yield('styles')

</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        @include('error.error')
        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                -->

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>



                    <!-- Nav Item - Messages -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <img src="{{ asset('img/logo_oriental_512.jpeg') }}" height="70" />

                        {{-- <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope fa-fw"></i>
                            <!-- Counter - Messages -->
                            <span class="badge badge-danger badge-counter">7</span>
                        </a> --}}
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                            <h6 class="dropdown-header">
                                Message Center
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
                                    <div class="status-indicator bg-success"></div>
                                </div>
                                <div class="font-weight-bold">
                                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem Ive been having.</div>
                                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
                                    <div class="status-indicator"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                                    <div class="small text-gray-500">Jae Chun · 1d</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
                                    <div class="status-indicator bg-warning"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                                    <div class="status-indicator bg-success"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                        </div>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>



                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <section>
                    <div class="col-sm-12" style="text-align: center; padding: 10px 0 10px 0; background: #D8020C; color: #fff;">
                      <h4> Politica y Privacidad | 정책 및 개인 정보</h4>
                    </div>
                  </section>

                    <br />

                    <div class="container">




                      <div class="row">
                            <p>
                                <b>OrientalPY</b>
                                Gracias por visitar nuestra App. Esta declaración de privacidad describe nuestras prácticas de información en línea y las elecciones que podemos hacer sobre la información recopilada a través de nuestros sitios web, servicios y aplicaciones ("Sitio" o colectivamente "Sitio" respectivamente). Esta política de privacidad se aplica a cualquier sitio que se vincule directamente a esta política. Para ciertas ofertas en nuestro sitio, puede haber avisos adicionales con respecto a nuestras prácticas y opciones de información. La política de privacidad está diseñada para proporcionar transparencia en las prácticas y principios de privacidad.
                            </p>

                            <p>
                                <b>Información que recopilamos</b>
                                Podemos recopilar varios tipos de información mientras interactúa con el sitio y a través de publicidad y medios a través de Internet y aplicaciones móviles. Esta información incluye información personal (como nombre, número de teléfono, dirección postal, dirección de correo electrónico e información de pago específica), información técnica (como identificador de dispositivo, dirección IP, tipo de navegador, información técnica, incluido el sistema operativo) e información de uso. Puede ser incluido (Por ejemplo, cómo usar y navegar por el sitio, información sobre el contenido o anuncios que el usuario ha mostrado o hecho clic). Combinamos este tipo de información y colectivamente nos referimos a toda esta información como "información" en esta Política de privacidad. Como se describe a continuación, podemos utilizar cookies, balizas web, píxeles y otras tecnologías similares para recopilar información en nuestro nombre o en nuestro nombre. Los tipos de información que se pueden recopilar se describen a continuación.
                            </p>

                            <p>
                                <b>Otra información que nos proporcione</b>
                                Algunos sitios le permiten compartir otra información sobre usted, como publicar y compartir información en el sitio web del sitio o en la comunidad del sitio. A veces, puede optar por proporcionarnos información personal más sensible, como información sobre su salud física y mental, datos biológicos, raza u origen étnico, creencias religiosas o filosóficas, vida sexual, orientación sexual, opiniones políticas o sindicatos. miembro. Por ejemplo, si participa en una encuesta, grupo focal u oportunidad para probar un nuevo producto, programa o servicio, podemos recopilar este tipo de información confidencial. Recopilamos esta información confidencial con su consentimiento si así lo exige la ley y tomamos medidas especiales para proteger y limitar el uso de la información de acuerdo con el propósito para el que se utiliza.
                            </p>

                            <p>
                                <b>Información de otras personas para invitar amigos</b>
                                Algunos sitios pueden recopilar información enviada por otros sobre usted. Por ejemplo, puede invitar a un amigo a enviar información, participar en la oferta, recomendarla o compartir contenido. Al procesar estas solicitudes, podemos recibir su información, incluido el nombre del destinatario, la dirección postal, la dirección de correo electrónico, el número de teléfono o la información sobre su interés y el uso de diversos productos, programas, servicios y contenido. En algunos sitios, los usuarios también pueden invitar a un amigo a una actividad proporcionando los detalles de contacto del amigo o importando un contacto de una libreta de direcciones u otro sitio.
                            </p>

                            <p>
                                <b>Información de otras fuentes.</b>
                                En algunos casos, podemos combinar información recibida en línea con otra información, incluida información sobre el uso de otros sitios y publicidad y medios en línea. Además, la información puede complementarse o combinarse con información de una variedad de otras fuentes o registros externos, como datos demográficos, historial de transacciones o información personal, y dicha información combinada puede usarse de acuerdo con esta Política de privacidad.
                            </p>

                            <p>
                                <b>Foro publico</b>
                                Si publica o comparte esta información o información o contenido, como fotos, cartas, videos o comentarios, según su configuración de privacidad mientras participa en los foros en línea de nuestro sitio o interactúa con el sitio a través de sitios de redes sociales, el contenido y el nombre de usuario Se puede publicar en Internet o en la comunidad de usuarios. Después de compartir la información en el foro público, esta información ya no está disponible. Para obtener más información sobre cómo personalizar su configuración de privacidad en los sitios de redes sociales y cómo esos sitios de redes sociales manejan su información y contenido personal, consulte la Guía de ayuda de privacidad, la Política de privacidad y los Términos de uso correspondientes.
                            </p>

                            <p>
                                <b>Información sobre la ubicación</b>
                                Podemos acceder a cierta información sobre su ubicación, como su país o dirección, cuando la proporciona o mediante la información del dispositivo (como su dirección IP). Cuando accede a nuestro sitio desde su dispositivo móvil, podemos recopilar información sobre la ubicación exacta de su dispositivo.
                            </p>

                            <p>
                                <b>Información técnica y de uso.</b>
                                También proporcionamos cierta información técnica y de uso, como el tipo de dispositivo, navegador y sistema operativo que está utilizando, su proveedor de servicios de Internet u operador móvil, identificador único de dispositivo, IDFA o IDFV, dirección MAC e IP cuando utiliza nuestro sitio. Para recoger. Direcciones, configuración del dispositivo y del navegador, páginas web y aplicaciones móviles que usa, anuncios que ve e interactúa e información específica sobre el uso del sitio. Para obtener más información sobre cómo usamos estas tecnologías para recopilar esta información, consulte la sección Cookies y otra información técnica.
                            </p>
                      </div>
                      <hr>
                      <div class="row">

          <p>
            <b>  오리엔탈 쇼핑 </b>
            자산을 방문해 주셔서 감사합니다. 이 개인 정보 보호 정책은 당사의 온라인 정보 관행과 웹 사이트, 서비스 및 응용 프로그램 (각각 "사이트"또는 집합 적으로 "사이트")을 통해 수집 된 정보에 대해 선택할 수있는 사항에 대해 설명합니다. 이 개인 정보 보호 정책은이 정책에 직접 연결되는 모든 사이트에 적용됩니다. 당사 사이트의 특정 오퍼링에 대해서는 당사의 정보 관행 및 선택에 관한 추가 통지가있을 수 있습니다. 추가 개인 정보 공개를 읽고 귀하에게 어떻게 적용되는지 이해하십시오. 개인 정보 보호 정책은 개인 정보 보호 관행 및 원칙에 투명성을 제공하도록 설계되었습니다.

          </p>

          <p>
            <b>수집하는 정보</b>
            당사는 귀하가 사이트와 상호 작용하는 동안과 인터넷 및 모바일 앱을 통한 광고 및 미디어를 통해 다양한 유형의 정보를 수집 할 수 있습니다. 이 정보에는 개인 정보 (예 : 이름, 전화 번호, 우편 주소, 전자 메일 주소 및 특정 지불 정보), 기술 정보 (예 : 장치 식별자, IP 주소, 브라우저 유형, 운영 체제를 포함한 기술 정보) 및 사용 정보가 포함될 수 있습니다. (예 : 사이트 사용 및 탐색 방법, 사용자가 보여 주거나 클릭 한 컨텐츠 또는 광고에 대한 정보). 당사는 이러한 유형의 정보를 결합하여 본 개인 정보 보호 정책에서이 모든 정보를 "정보"라고 통칭합니다. 아래에 기술 된 바와 같이 쿠키, 웹 비콘, 픽셀 및 기타 유사한 기술을 사용하여 당사 또는 당사를 대신하여 정보를 수집 할 수 있습니다. 아래에서는 수집 할 수있는 정보 유형에 대해 설명합니다.
          </p>

          <p>
            <b>귀하가 당사에 제공 한 기타 정보</b>
            일부 사이트에서는 사이트의 웹 사이트 나 사이트 커뮤니티에 정보를 게시하고 공유하는 등 자신에 대한 다른 정보를 공유 할 수 있습니다. 때로는 신체적, 정신적 건강, 생체 데이터, 인종 또는 민족, 종교적 또는 철학적 신념, 성생활, 성적 취향, 정치적 견해 또는 노동 조합에 관한 정보와 같이보다 민감한 개인 정보를 당사에 제공하도록 선택할 수도 있습니다. 회원. 예를 들어 설문 조사, 포커스 그룹 또는 신제품, 프로그램 또는 서비스를 테스트 할 기회에 참여하는 경우 이러한 유형의 민감한 정보를 수집 할 수 있습니다. 우리는 법률에 의해 요구되는 경우 귀하의 동의하에 이러한 민감한 정보를 수집하며, 정보의 사용 목적에 따라 정보의 사용을 보호하고 제한하기위한 특별한 조치를 취합니다.
          </p>

          <p>
            <b>친구를 초대하는 다른 사람의 정보</b>
            일부 사이트에서는 다른 사람들이 귀하에 대해 제출 한 정보를 수집 할 수 있습니다. 예를 들어 친구가 정보를 제출하여 오퍼링에 참여하거나 추천하거나 컨텐츠를 공유하도록 초대 할 수 있습니다. 이러한 요청을 처리함으로써 수취인의 이름, 우편 주소, 이메일 주소, 전화 번호 또는 다양한 제품, 프로그램, 서비스 및 컨텐츠에 대한 귀하의 관심 및 사용에 관한 정보를 포함하여 귀하의 정보를 수신 할 수 있습니다. 일부 사이트에서는 사용자가 친구의 연락처 세부 정보를 제공하거나 주소록 또는 다른 사이트에서 연락처를 가져 와서 친구를 활동에 초대 할 수도 있습니다.
          </p>

          <p>
            <b>다른 출처의 정보</b>
            당사는 경우에 따라 다른 사이트 및 온라인 광고 및 미디어의 사용 정보를 포함하여 온라인으로받은 정보를 다른 정보와 결합 할 수 있습니다. 또한 인구 통계, 거래 내역 또는 개인 정보와 같은 다양한 다른 출처 또는 외부 기록의 정보로 정보를 보완하거나 결합 할 수 있으며,이 개인 정보 보호 정책에 따라 이러한 결합 된 정보를 사용할 수 있습니다.
          </p>

          <p>
            <b>공개 포럼</b>
            당사 사이트의 온라인 포럼에 참여하는 동안 또는 소셜 미디어 사이트를 통해 사이트와 상호 작용할 때 개인 정보 설정에 따라이 정보 또는 사진, 편지, 비디오 또는 의견과 같은 정보 또는 콘텐츠를 게시하거나 공유하는 경우 콘텐츠 및 사용자 이름은 인터넷 또는 사용자 커뮤니티에서 공개 될 수 있습니다. 공개 된 포럼에서 정보를 공유 한 후에는이 정보를 더 이상 사용할 수 없습니다. 소셜 미디어 사이트에서 개인 정보 설정을 사용자 정의하는 방법 및 해당 소셜 미디어 사이트가 개인 정보 및 콘텐츠를 처리하는 방법에 대한 자세한 내용은 해당 개인 정보 보호 도움말 가이드, 개인 정보 보호 정책 및 이용 약관을 참조하십시오.
          </p>

          <p>
            <b>위치 정보</b>
            당사는 귀하가 제공 할 때 또는 장치 정보 (예 : IP 주소)를 통해 귀하의 국가 또는 주소와 같은 귀하의 위치에 관한 특정 정보에 액세스 할 수 있습니다. 귀하가 귀하의 모바일 장치에서 당사 사이트에 액세스하면 당사는 귀하의 장치의 정확한 위치에 대한 정보를 수집 할 수 있습니다.
          </p>

          <p>
            <b>기술 및 사용 정보</b>
            또한 당사는 귀하가 당사 사이트를 사용할 때 사용중인 장치, 브라우저 및 운영 체제 유형, 인터넷 서비스 제공 업체 또는 이동 통신 업체, 고유 한 장치 식별자, IDFA 또는 IDFV, MAC 주소, IP와 같은 특정 기술 및 사용 정보를 수집합니다. 주소, 기기 및 브라우저 설정, 사용하는 웹 페이지 및 모바일 앱,보고 상호 작용하는 광고 및 특정 사이트 사용 정보 이러한 기술을 사용하여이 정보를 수집하는 방법에 대한 자세한 내용은 쿠키 및 기타 기술 정보 섹션을 참조하십시오.
          </p>

                      </div>
                    </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; </span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/app.js') }}"></script>
{{-- solo de prueba --}}


<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

{{-- dattables --}}
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.jqueryui.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.jqueryui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

{{-- icluire archivos para una vista de arbol si da problemas eliminar --}}
<script src="{{ asset('vista_arbol/jstree.min.js') }}"></script>
   {{-- estilo de buscador se utiliza en la vista de productos --}}
<script src="{{ asset('select2/js/select2.min.js') }}"></script>

@include('admin.scripts')
@include('comunes.js')
@yield('script')
@yield('scripts')

</body>
</html>
