class PlanModel {
  const PlanModel({
    required this.id,
    required this.name,
    required this.price,
    required this.durationDays,
    this.description,
  });

  final int id;
  final String name;
  final double price;
  final int durationDays;
  final String? description;

  factory PlanModel.fromJson(Map<String, dynamic> json) {
    return PlanModel(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0,
      durationDays: json['duration_days'] as int? ?? 0,
      description: json['description'] as String?,
    );
  }
}
