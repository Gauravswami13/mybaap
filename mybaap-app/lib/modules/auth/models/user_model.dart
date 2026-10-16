class UserModel {
  const UserModel({
    required this.id,
    required this.mobile,
    required this.status,
    this.name,
    this.photoUrl,
  });

  final int id;
  final String mobile;
  final String? name;
  final String? photoUrl;
  final String status;

  String get displayName => (name == null || name!.trim().isEmpty) ? mobile : name!.trim();

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      mobile: json['mobile'] as String,
      name: json['name'] as String?,
      photoUrl: json['photo_url'] as String?,
      status: json['status'] as String? ?? 'active',
    );
  }
}

class SubscriptionStatus {
  const SubscriptionStatus({
    required this.isActive,
    this.planName,
    this.endDate,
  });

  final bool isActive;
  final String? planName;
  final String? endDate;

  factory SubscriptionStatus.fromProfile(dynamic data) {
    if (data is! Map) return const SubscriptionStatus(isActive: false);
    final subscription = data['subscription'];
    if (subscription is! Map) return const SubscriptionStatus(isActive: false);
    return SubscriptionStatus(
      isActive: subscription['is_active'] == true,
      planName: subscription['plan_name'] as String?,
      endDate: subscription['end_date'] as String?,
    );
  }
}
