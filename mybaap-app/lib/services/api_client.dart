import 'package:dio/dio.dart';
import 'package:mybaap/core/app_config.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/token_store.dart';

class ApiException implements Exception {
  ApiException(this.message);
  final String message;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient(this.tokenStore) {
    dio = Dio(
      BaseOptions(
        baseUrl: AppConfig.baseUrl,
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 20),
        headers: {'Accept': 'application/json'},
      ),
    );
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await tokenStore.read();
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
        onError: (error, handler) async {
          final status = error.response?.statusCode;
          final hadToken = error.requestOptions.headers['Authorization'] != null;
          if (status == 401 && hadToken) {
            await tokenStore.clear();
            AppRoutes.navigatorKey.currentState?.pushNamedAndRemoveUntil(AppRoutes.mobile, (route) => false);
          }
          handler.next(error);
        },
      ),
    );
  }

  final TokenStore tokenStore;
  late final Dio dio;

  Future<dynamic> get(String path, {Map<String, dynamic>? query}) async {
    return _send(() => dio.get(path, queryParameters: query));
  }

  Future<dynamic> post(String path, {Map<String, dynamic>? body}) async {
    return _send(() => dio.post(path, data: body));
  }

  Future<dynamic> put(String path, {Map<String, dynamic>? body}) async {
    return _send(() => dio.put(path, data: body));
  }

  Future<dynamic> _send(Future<Response<dynamic>> Function() call) async {
    try {
      final response = await call();
      final body = response.data;
      if (body is Map && body['success'] == true) {
        return body['data'];
      }
      throw ApiException(_message(body) ?? 'Request failed.');
    } on DioException catch (error) {
      throw ApiException(_message(error.response?.data) ?? 'Unable to connect. Please try again.');
    }
  }

  String? _message(dynamic body) {
    if (body is! Map) return null;
    final errors = body['data'];
    if (errors is Map && errors['errors'] is Map) {
      final map = errors['errors'] as Map;
      if (map.isNotEmpty) {
        final first = map.values.first;
        if (first is List && first.isNotEmpty) return first.first.toString();
      }
    }
    final message = body['message'];
    if (message is String && message.isNotEmpty) return message;
    return null;
  }
}
