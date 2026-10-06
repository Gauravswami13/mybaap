import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';

void main() {
  testWidgets('primary button shows its label', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: CustomButton(text: 'Send OTP', onPressed: () {}),
        ),
      ),
    );

    expect(find.text('Send OTP'), findsOneWidget);
  });
}
