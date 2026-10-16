class BannerItem {
  const BannerItem({required this.id, required this.title, this.imageUrl, this.link});

  final int id;
  final String title;
  final String? imageUrl;
  final String? link;

  factory BannerItem.fromJson(Map<String, dynamic> json) {
    return BannerItem(
      id: json['id'] as int,
      title: json['title'] as String? ?? '',
      imageUrl: json['image_url'] as String?,
      link: json['link'] as String?,
    );
  }
}

class CategoryItem {
  const CategoryItem({required this.id, required this.name, required this.slug, this.imageUrl});

  final int id;
  final String name;
  final String slug;
  final String? imageUrl;

  factory CategoryItem.fromJson(Map<String, dynamic> json) {
    return CategoryItem(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      slug: json['slug'] as String? ?? '',
      imageUrl: json['image_url'] as String?,
    );
  }
}

class VideoItem {
  const VideoItem({
    required this.id,
    required this.title,
    required this.isPaid,
    required this.locked,
    this.description,
    this.thumbnailUrl,
    this.categoryName,
    this.duration,
  });

  final int id;
  final String title;
  final String? description;
  final String? thumbnailUrl;
  final String? categoryName;
  final int? duration;
  final bool isPaid;
  final bool locked;

  factory VideoItem.fromJson(Map<String, dynamic> json) {
    final category = json['category'];
    return VideoItem(
      id: json['id'] as int,
      title: json['title'] as String? ?? '',
      description: json['description'] as String?,
      thumbnailUrl: json['thumbnail_url'] as String?,
      categoryName: category is Map ? category['name'] as String? : null,
      duration: json['duration'] as int?,
      isPaid: json['is_paid'] == true,
      locked: json['locked'] == true,
    );
  }

  String get durationLabel {
    final seconds = duration;
    if (seconds == null) return '';
    final minutes = seconds ~/ 60;
    final remain = seconds % 60;
    if (minutes >= 60) {
      return '${minutes ~/ 60}h ${minutes % 60}m';
    }
    return '${minutes}m ${remain.toString().padLeft(2, '0')}s';
  }
}
