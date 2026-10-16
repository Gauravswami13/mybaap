import 'package:flutter/foundation.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/services/api_client.dart';

class HomeProvider extends ChangeNotifier {
  HomeProvider(this.api);

  final ApiClient api;

  bool bootstrapped = false;
  bool loading = false;
  String? error;
  List<BannerItem> banners = [];
  List<CategoryItem> categories = [];
  List<VideoItem> videos = [];
  List<VideoItem> catalog = [];
  bool catalogReady = false;
  int? activeCategoryId;
  bool categoryLoading = false;
  bool categoryReady = false;
  String? categoryError;
  List<VideoItem> categoryVideos = [];

  Future<void> bootstrap(Future<void> Function() loadProfile) async {
    if (bootstrapped) return;
    bootstrapped = true;
    await Future.wait([load(), loadProfile()]);
  }

  Future<void> load() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final data = await api.get('/home');
      if (data is Map) {
        banners = _list(data['banners']).map(BannerItem.fromJson).toList();
        categories = _list(data['categories']).map(CategoryItem.fromJson).toList();
        videos = _list(data['videos']).map(VideoItem.fromJson).toList();
      }
      categoryReady = false;
      catalogReady = false;
      loading = false;
      notifyListeners();
    } on ApiException catch (exception) {
      loading = false;
      error = exception.message;
      notifyListeners();
    } catch (_) {
      loading = false;
      error = 'Unable to load videos.';
      notifyListeners();
    }
  }

  Future<void> loadCatalog({bool force = false}) async {
    if (!force && catalogReady) return;
    try {
      final data = await api.get('/videos');
      if (data is Map) {
        catalog = _list(data['videos']).map(VideoItem.fromJson).toList();
      }
      catalogReady = true;
      notifyListeners();
    } on ApiException {
      catalogReady = false;
    }
  }

  Future<void> loadCategory(int id, {bool force = false}) async {
    if (!force && activeCategoryId == id && (categoryLoading || categoryReady)) return;
    activeCategoryId = id;
    categoryLoading = true;
    categoryReady = false;
    categoryError = null;
    categoryVideos = [];
    notifyListeners();
    try {
      final data = await api.get('/videos', query: {'category_id': id});
      if (activeCategoryId != id) return;
      if (data is Map) {
        categoryVideos = _list(data['videos']).map(VideoItem.fromJson).toList();
      }
      categoryLoading = false;
      categoryReady = true;
      notifyListeners();
    } on ApiException catch (exception) {
      if (activeCategoryId != id) return;
      categoryLoading = false;
      categoryReady = true;
      categoryError = exception.message;
      notifyListeners();
    } catch (_) {
      if (activeCategoryId != id) return;
      categoryLoading = false;
      categoryReady = true;
      categoryError = 'Unable to load videos.';
      notifyListeners();
    }
  }

  Future<String> playbackUrl(int id) async {
    final data = await api.get('/videos/$id/playback');
    if (data is Map && data['url'] is String) {
      return data['url'] as String;
    }
    throw ApiException('Playback URL was not returned.');
  }

  List<Map<String, dynamic>> _list(dynamic value) {
    if (value is! List) return [];
    return value.whereType<Map>().map((item) => Map<String, dynamic>.from(item)).toList();
  }
}
